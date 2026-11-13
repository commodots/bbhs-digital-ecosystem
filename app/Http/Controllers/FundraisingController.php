<?php
namespace App\Http\Controllers;
use App\Models\FundraisingCampaign;
use App\Models\FundraisingDonation;
use App\Models\FundraisingPledge;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
class FundraisingController extends Controller
{
    private function campaignData(FundraisingCampaign $c): array {
        $target=(int)$c->target_amount_kobo; $raised=(int)$c->raised_amount_kobo;
        $image=$c->image ? (str_starts_with($c->image,'http') || str_starts_with($c->image,'/') ? $c->image : '/storage/'.ltrim($c->image,'/')) : '/images/bbhs/school/bbhs-school-gate.jpg';
        return ['id'=>$c->id,'title'=>$c->title,'slug'=>$c->slug,'category'=>$c->category,'scope_type'=>$c->scope_type,'excerpt'=>$c->excerpt,'description'=>$c->description,'image'=>$image,'target_amount'=>$target/100,'raised_amount'=>$raised/100,'progress'=>$target>0?min(100,round($raised/$target*100)):0,'starts_at'=>$c->starts_at?->format('d M Y'),'ends_at'=>$c->ends_at?->format('d M Y'),'donor_count'=>$c->donations()->where('status','success')->count()];
    }
    public function index() {
        $campaigns=FundraisingCampaign::where('published',true)->where('status','active')->latest()->paginate(12)->through(fn($c)=>$this->campaignData($c));
        return Inertia::render('Fundraising/Index',['campaigns'=>$campaigns]);
    }
    public function show(Request $request, FundraisingCampaign $campaign) {
        abort_unless($campaign->published && $campaign->status==='active',404);
        $recent=$campaign->donations()->where('status','success')->latest('paid_at')->take(10)->get()->map(fn($d)=>['name'=>$d->is_anonymous?'Anonymous':($d->donor_name?:'BBHS Supporter'),'amount'=>$d->amount_kobo/100,'date'=>$d->paid_at?->format('d M Y')]);
        $member = $request->user();
        $profile = $member?->alumniProfile;
        return Inertia::render('Fundraising/Show',[
            'campaign'=>$this->campaignData($campaign),
            'recentDonations'=>$recent,
            'bankTransferDetails'=>config('bbhs.donations'),
            'memberDetails'=>$member ? [
                'name'=>$profile ? trim(($profile->first_name ?? '').' '.($profile->last_name ?? '')) : $member->name,
                'email'=>$member->email,
                'phone'=>$profile?->phone,
                'is_member'=>true,
            ] : null,
        ]);
    }
    public function donate(Request $request, FundraisingCampaign $campaign) {
        abort_unless($campaign->published && $campaign->status==='active',404);
        $data=$request->validate(['donor_name'=>['nullable','string','max:150'],'donor_email'=>['required','email','max:255'],'donor_phone'=>['nullable','string','max:40'],'amount'=>['required','numeric','min:100','max:100000000'],'is_anonymous'=>['nullable','boolean'],'method'=>['required','in:paystack,bank_transfer'],'transfer_reference'=>['nullable','string','max:150']]);
        if ($request->user()) {
            $profile = $request->user()->alumniProfile;
            $data['donor_name'] = $request->user()->name;
            $data['donor_email'] = $request->user()->email;
            $data['donor_phone'] = $profile?->phone;
        }
        if($data['method']==='bank_transfer') {
            $donation=$this->createDonation($request,$campaign,$data,'bank_transfer','pending');
            $donation->update(['transfer_reference'=>$data['transfer_reference']??null]);
            return redirect()->route('fundraising.show',$campaign->slug)->with('success','Your donation has been recorded as pending. Please complete the bank transfer and provide the transfer reference if you have not already done so. The Association will verify it.');
        }
        $donation=$this->createDonation($request,$campaign,$data,'paystack','pending');
        $secret=config('services.paystack.secret_key');
        if(!$secret) { $donation->update(['status'=>'failed']); return back()->withErrors(['payment'=>'Online payment is not currently configured.']); }
        $response=Http::withToken($secret)->acceptJson()->post('https://api.paystack.co/transaction/initialize',[
            'email'=>$data['donor_email'],'amount'=>$donation->amount_kobo,'reference'=>$donation->reference,'currency'=>'NGN',
            'callback_url'=>route('fundraising.callback'),
            'metadata'=>['purpose'=>'fundraising_donation','donation_id'=>$donation->id,'campaign_id'=>$campaign->id],
        ]);
        if(!$response->successful() || !$response->json('status')) { $donation->update(['status'=>'failed','gateway_response'=>$response->json()]); return back()->withErrors(['payment'=>'Unable to start Paystack checkout. Please try again.']); }
        return Inertia::location($response->json('data.authorization_url'));
    }
    private function createDonation(Request $request, FundraisingCampaign $campaign, array $data, string $method, string $status): FundraisingDonation {
        return FundraisingDonation::create([
            'campaign_id'=>$campaign->id,'user_id'=>$request->user()?->id,'donor_name'=>$data['donor_name']??null,
            'donor_email'=>$data['donor_email'],'donor_phone'=>$data['donor_phone']??null,'amount_kobo'=>(int)round(((float)$data['amount'])*100),
            'reference'=>'BBHSDON-'.Str::upper(Str::random(20)),'method'=>$method,'status'=>$status,
            'is_anonymous'=>(bool)($data['is_anonymous']??false),'transfer_reference'=>$data['transfer_reference']??null,
        ]);
    }
    public function callback(Request $request) {
        $reference=(string)$request->query('reference'); abort_unless($reference,404);
        $donation=FundraisingDonation::where('reference',$reference)->firstOrFail(); $this->verifyAndApply($donation);
        return redirect()->route('fundraising.show',$donation->campaign->slug)->with('success','Payment verification completed. Thank you for supporting BBHS. If successful, your donation will appear in the campaign totals.');
    }
    public function processWebhookReference(string $reference): void { $donation=FundraisingDonation::where('reference',$reference)->first(); if($donation) $this->verifyAndApply($donation); }
    public function webhook(Request $request) {
        $signature=$request->header('x-paystack-signature'); $secret=config('services.paystack.secret_key');
        abort_unless($secret && $signature && hash_equals(hash_hmac('sha512',$request->getContent(),$secret),$signature),401);
        $payload=$request->all();
        if(($payload['event']??'')==='charge.success' && !empty($payload['data']['reference'])) {
            $donation=FundraisingDonation::where('reference',$payload['data']['reference'])->first();
            if($donation) $this->verifyAndApply($donation);
        }
        return response()->json(['received'=>true]);
    }
    private function verifyAndApply(FundraisingDonation $donation): void {
        if($donation->status==='success' || $donation->method!=='paystack') return;
        $secret=config('services.paystack.secret_key'); if(!$secret) return;
        $response=Http::withToken($secret)->get('https://api.paystack.co/transaction/verify/'.urlencode($donation->reference));
        $data=$response->json('data');
        if(!$response->successful() || !$response->json('status') || ($data['status']??'')!=='success' || (int)($data['amount']??0)!==$donation->amount_kobo || ($data['currency']??'')!=='NGN') return;
        $applied=false;
        DB::transaction(function() use($donation,$data,&$applied) {
            $locked=FundraisingDonation::lockForUpdate()->find($donation->id);
            if(!$locked || $locked->status==='success') return;
            $locked->update(['status'=>'success','paid_at'=>now(),'gateway_reference'=>$data['id']??null,'gateway_response'=>$data]);
            $applied=true;
        });
        if($applied) {
            try {
                Mail::raw("Thank you for supporting Baptist Boys' High School Old Boys' Association.\n\nCampaign: ".$donation->campaign->title."\nAmount: ₦".number_format($donation->amount_kobo/100,2)."\nReference: ".$donation->reference."\nDate: ".now()->format('d M Y, h:i A')."\n\nThis email confirms receipt of your donation.",function($message) use($donation) { $message->to($donation->donor_email)->subject('BBHS donation receipt - '.$donation->reference); });
            } catch(\Throwable $e) { report($e); }
        }
    }
    public function myActivity(Request $request) {
        $user = $request->user();
        $donations = FundraisingDonation::with('campaign:id,title,slug')
            ->where('user_id', $user->id)->latest()->get()
            ->map(fn($d) => ['id'=>$d->id,'campaign'=>$d->campaign?->title,'slug'=>$d->campaign?->slug,'amount'=>$d->amount_kobo/100,'status'=>$d->status,'method'=>$d->method,'reference'=>$d->reference,'paid_at'=>$d->paid_at?->format('d M Y'),'anonymous'=>$d->is_anonymous]);
        $pledges = FundraisingPledge::with('campaign:id,title,slug')
            ->where('user_id', $user->id)->latest()->get()
            ->map(fn($p) => ['id'=>$p->id,'campaign'=>$p->campaign?->title,'slug'=>$p->campaign?->slug,'amount'=>$p->amount_kobo/100,'status'=>$p->status,'created_at'=>$p->created_at?->format('d M Y'),'fulfilled_at'=>$p->fulfilled_at?->format('d M Y')]);
        return Inertia::render('Fundraising/MyActivity', ['donations'=>$donations,'pledges'=>$pledges]);
    }

    public function pledge(Request $request, FundraisingCampaign $campaign) {
        abort_unless($campaign->published && $campaign->status==='active',404);
        $data=$request->validate(['name'=>['required','string','max:150'],'email'=>['required','email','max:255'],'phone'=>['nullable','string','max:40'],'amount'=>['required','numeric','min:100','max:100000000'],'note'=>['nullable','string','max:1000']]);
        if ($request->user()) {
            $profile = $request->user()->alumniProfile;
            $data['name'] = $request->user()->name;
            $data['email'] = $request->user()->email;
            $data['phone'] = $profile?->phone;
        }
        FundraisingPledge::create(['campaign_id'=>$campaign->id,'user_id'=>$request->user()?->id,'name'=>$data['name'],'email'=>$data['email'],'phone'=>$data['phone']??null,'amount_kobo'=>(int)round($data['amount']*100),'note'=>$data['note']??null]);
        return back()->with('success','Thank you. Your pledge has been recorded. The Association may contact you with fulfilment details.');
    }
}
