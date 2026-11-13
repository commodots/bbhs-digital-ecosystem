<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GraduationSet;
use App\Models\FundraisingCampaign;
use App\Models\FundraisingDonation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
class AdminFundraisingController extends Controller
{
    public function index() {
        $campaigns=FundraisingCampaign::withCount(['donations as successful_donations_count'=>fn($q)=>$q->where('status','success')])->latest()->paginate(20)->through(fn($c)=>[
            'id'=>$c->id,'title'=>$c->title,'slug'=>$c->slug,'scope_type'=>$c->scope_type,'status'=>$c->status,'published'=>$c->published,
            'target_amount'=>$c->target_amount_kobo/100,'raised_amount'=>$c->raised_amount_kobo/100,'donations_count'=>$c->successful_donations_count,
        ]);
        return Inertia::render('Admin/Fundraising/Index',['campaigns'=>$campaigns,'stats'=>[
            'campaigns'=>FundraisingCampaign::count(),'active'=>FundraisingCampaign::where('published',true)->where('status','active')->count(),
            'raised'=>FundraisingDonation::where('status','success')->sum('amount_kobo')/100,
            'pending'=>FundraisingDonation::where('status','pending')->count(),
        ]]);
    }
    public function create() { return Inertia::render('Admin/Fundraising/Form',['campaign'=>null,'sets'=>GraduationSet::orderBy('graduation_year')->get(['id','name','graduation_year']),'branches'=>Branch::orderBy('name')->get(['id','name'])]); }
    public function store(Request $request) {
        $data=$this->validateCampaign($request); $data['slug']=$this->slug($data['title']); $data['created_by']=$request->user()->id;
        $data['published']=(bool)($data['published']??false); $data['status']=$data['published']?'active':'draft';
        if($request->hasFile('image')) $data['image']=$request->file('image')->store('fundraising','public');
        if($data['published']) { $data['approved_by']=$request->user()->id; $data['approved_at']=now(); }
        FundraisingCampaign::create($data);
        return redirect()->route('admin.fundraising.index')->with('success','Fundraising campaign created.');
    }
    public function edit(FundraisingCampaign $campaign) { return Inertia::render('Admin/Fundraising/Form',['campaign'=>$campaign,'sets'=>GraduationSet::orderBy('graduation_year')->get(['id','name','graduation_year']),'branches'=>Branch::orderBy('name')->get(['id','name'])]); }
    public function update(Request $request, FundraisingCampaign $campaign) {
        $data=$this->validateCampaign($request); $data['slug']=$this->slug($data['title'],$campaign->id); $data['published']=(bool)($data['published']??false);
        $data['status']=$data['published']?'active':'draft';
        if($request->hasFile('image')) { if($campaign->image) Storage::disk('public')->delete($campaign->image); $data['image']=$request->file('image')->store('fundraising','public'); }
        else unset($data['image']);
        if($data['published'] && !$campaign->approved_at) { $data['approved_by']=$request->user()->id; $data['approved_at']=now(); }
        $campaign->update($data);
        return redirect()->route('admin.fundraising.index')->with('success','Campaign updated.');
    }
    public function destroy(FundraisingCampaign $campaign) {
        if($campaign->donations()->exists() || $campaign->pledges()->exists()) return back()->withErrors(['campaign'=>'Campaigns with donations or pledges cannot be deleted. Please close or unpublish it instead.']);
        if($campaign->image) Storage::disk('public')->delete($campaign->image);
        $campaign->delete(); return back()->with('success','Campaign deleted.');
    }
    public function donations(Request $request) {
        $q=FundraisingDonation::with('campaign:id,title')->latest();
        if($request->filled('status')) $q->where('status',$request->string('status'));
        if($request->filled('search')) $q->where(fn($x)=>$x->where('donor_name','like','%'.$request->search.'%')->orWhere('donor_email','like','%'.$request->search.'%')->orWhere('reference','like','%'.$request->search.'%'));
        $donations=$q->paginate(30)->withQueryString()->through(fn($d)=>['id'=>$d->id,'campaign'=>$d->campaign?->title,'donor_name'=>$d->is_anonymous?'Anonymous':$d->donor_name,'donor_email'=>$d->donor_email,'amount'=>$d->amount_kobo/100,'reference'=>$d->reference,'method'=>$d->method,'status'=>$d->status,'transfer_reference'=>$d->transfer_reference,'paid_at'=>$d->paid_at?->format('d M Y H:i'),'created_at'=>$d->created_at->format('d M Y H:i')]);
        return Inertia::render('Admin/Fundraising/Donations',['donations'=>$donations,'filters'=>$request->only('status','search')]);
    }
    public function verifyDonation(Request $request, FundraisingDonation $donation) {
        $data=$request->validate(['action'=>['required','in:approve,reject'],'admin_note'=>['nullable','string','max:1000']]);
        abort_unless($donation->method==='bank_transfer' && $donation->status==='pending',422);
        $donation->update(['status'=>$data['action']==='approve'?'success':'failed','paid_at'=>$data['action']==='approve'?now():null,'verified_by'=>$request->user()->id,'admin_note'=>$data['admin_note']??null]);
        if ($data['action']==='approve') {
            try { Mail::raw("Thank you for supporting BBHS. Your bank transfer donation has been verified.\nCampaign: ".$donation->campaign->title."\nAmount: ₦".number_format($donation->amount_kobo/100,2)."\nReference: ".$donation->reference, function($message) use($donation) { $message->to($donation->donor_email)->subject('BBHS donation receipt - '.$donation->reference); }); } catch(\Throwable $e) { report($e); }
        }
        return back()->with('success','Donation review completed.');
    }
    public function pledges() {
        $pledges=\App\Models\FundraisingPledge::with('campaign:id,title')->latest()->paginate(30)->through(fn($p)=>['id'=>$p->id,'campaign'=>$p->campaign?->title,'name'=>$p->name,'email'=>$p->email,'amount'=>$p->amount_kobo/100,'status'=>$p->status,'created_at'=>$p->created_at->format('d M Y')]);
        return Inertia::render('Admin/Fundraising/Pledges',['pledges'=>$pledges]);
    }
    private function validateCampaign(Request $request): array {
        $data = $request->validate([
            'title'=>['required','string','max:255'],'category'=>['nullable','string','max:100'],
            'scope_type'=>['required','in:national,set,branch'],'scope_id'=>['nullable','integer'],
            'excerpt'=>['nullable','string','max:1000'],'description'=>['nullable','string'],
            'image'=>['nullable','image','mimes:jpg,jpeg,png,webp','max:5120'],
            'target_amount'=>['required','numeric','min:0'],'starts_at'=>['nullable','date'],'ends_at'=>['nullable','date','after_or_equal:starts_at'],
            'published'=>['nullable','boolean'],'status'=>['nullable','in:draft,active,closed'],
        ]);
        if ($data['scope_type']==='set') {
            validator($data,['scope_id'=>['required','integer','exists:graduation_sets,id']])->validate();
        } elseif ($data['scope_type']==='branch') {
            validator($data,['scope_id'=>['required','integer','exists:branches,id']])->validate();
        } else {
            $data['scope_id']=null;
        }
        $data['target_amount_kobo']=(int)round(((float)$data['target_amount'])*100);
        unset($data['target_amount']);
        return $data;
    }
    private function slug(string $title, ?int $ignore=null): string {
        $base=Str::slug($title)?:'campaign'; $slug=$base; $n=1;
        while(FundraisingCampaign::where('slug',$slug)->when($ignore,fn($q)=>$q->where('id','!=',$ignore))->exists()) $slug=$base.'-'.$n++;
        return $slug;
    }
}
