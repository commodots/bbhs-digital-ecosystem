<?php
namespace App\Http\Controllers;
use App\Models\MembershipDuesAccount;
use App\Models\MembershipDuesPayment;
use App\Models\MembershipDuesAuditLog;
use App\Notifications\BbhsPlatformNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class MembershipDuesController extends Controller {
 private function account($user): MembershipDuesAccount { return MembershipDuesAccount::firstOrCreate(['user_id'=>$user->id],['commencement_date'=>$user->created_at?->toDateString() ?? today(),'plan'=>'annual','annual_amount_kobo'=>1250000,'monthly_amount_kobo'=>105000,'next_due_at'=>$user->created_at?->toDateString() ?? today(),'status'=>'due']); }
 public function index(Request $request) {
  $user=$request->user(); $account=$this->account($user);
  return Inertia::render('Dues/Index',['account'=>['plan'=>$account->plan,'commencement_date'=>$account->commencement_date?->format('Y-m-d'),'paid_through'=>$account->paid_through?->format('Y-m-d'),'next_due_at'=>$account->next_due_at?->format('Y-m-d'),'status'=>$account->computed_status,'exempt'=>$account->exempt,'annual_amount'=>$account->annual_amount_kobo/100,'monthly_amount'=>$account->monthly_amount_kobo/100], 'payments'=>$account->payments()->latest()->get()->map(fn($p)=>['id'=>$p->id,'reference'=>$p->reference,'amount'=>$p->amount_kobo/100,'period_type'=>$p->period_type,'method'=>$p->method,'status'=>$p->status,'paid_at'=>$p->paid_at?->format('d M Y'),'created_at'=>$p->created_at->format('d M Y')]),'paymentEnabled'=>config('bbhs.dues.allow_pending_payment',true) || $user->member_status==='approved','paystackEnabled'=>filled(config('services.paystack.secret_key'))]);
 }
 public function choosePlan(Request $request) { $data=$request->validate(['plan'=>['required','in:annual,monthly']]); $this->account($request->user())->update(['plan'=>$data['plan']]); return back()->with('success','Your dues payment preference has been updated.'); }
 public function initialize(Request $request) {
  $data=$request->validate(['plan'=>['required','in:annual,monthly']]); $user=$request->user();
  if(!config('bbhs.dues.allow_pending_payment',true) && $user->member_status!=='approved') abort(403,'Payment is available after membership approval.');
  $account=$this->account($user); if($account->exempt) return back()->with('error','Your membership dues are currently exempt.');
  $amount=$data['plan']==='annual'?$account->annual_amount_kobo:$account->monthly_amount_kobo; $reference='BBHSDUES-'.Str::upper(Str::random(18));
  $payment=$account->payments()->create(['user_id'=>$user->id,'reference'=>$reference,'amount_kobo'=>$amount,'period_type'=>$data['plan'],'method'=>'paystack','status'=>'pending']);
  $response=Http::withToken(config('services.paystack.secret_key'))->acceptJson()->post('https://api.paystack.co/transaction/initialize',['email'=>$user->email,'amount'=>$amount,'reference'=>$reference,'currency'=>'NGN','callback_url'=>route('dues.callback'),'metadata'=>['purpose'=>'membership_dues','payment_id'=>$payment->id,'user_id'=>$user->id]]);
  if(!$response->successful() || !$response->json('status')) { $payment->update(['status'=>'failed','gateway_response'=>$response->json()]); return back()->withErrors(['payment'=>'Unable to start Paystack checkout. Please try again or contact the Association.']); }
  return Inertia::location($response->json('data.authorization_url'));
 }
 public function callback(Request $request) { $reference=(string)$request->query('reference'); abort_unless($reference,404); $payment=MembershipDuesPayment::where('reference',$reference)->firstOrFail(); abort_unless($payment->user_id===$request->user()?->id,403); $this->verifyAndApply($payment); return redirect()->route('dues.index')->with('success','Payment verification completed. Please review your payment status below.'); }
 public function webhook(Request $request) {
  $signature=$request->header('x-paystack-signature'); $secret=config('services.paystack.secret_key'); abort_unless($secret && $signature && hash_equals(hash_hmac('sha512',$request->getContent(),$secret),$signature),401);
  $payload=$request->all(); if(($payload['event']??'')==='charge.success' && !empty($payload['data']['reference'])) {
   $reference=(string)$payload['data']['reference'];
   $payment=MembershipDuesPayment::where('reference',$reference)->first();
   if($payment) $this->verifyAndApply($payment);
   else app(\App\Http\Controllers\FundraisingController::class)->processWebhookReference($reference);
  }
  return response()->json(['received'=>true]);
 }
 private function currentCycleStart(MembershipDuesAccount $account, string $period): \Carbon\Carbon {
  $start=\Carbon\Carbon::parse($account->commencement_date ?? today())->startOfDay();
  $today=today();
  while(($period==='annual'?$start->copy()->addYear():$start->copy()->addMonthNoOverflow())->lte($today)) { $start=$period==='annual'?$start->addYear():$start->addMonthNoOverflow(); }
  return $start;
 }
 private function verifyAndApply(MembershipDuesPayment $payment): void {
  if($payment->status==='success') return;
  $secret=config('services.paystack.secret_key'); if(!$secret) return;
  $response=Http::withToken($secret)->get('https://api.paystack.co/transaction/verify/'.urlencode($payment->reference));
  $data=$response->json('data');
  if(!$response->successful() || !$response->json('status') || ($data['status']??'')!=='success' || (int)($data['amount']??0)!==$payment->amount_kobo || ($data['currency']??'')!=='NGN') return;

  $applied=false;
  DB::transaction(function() use($payment,$data,&$applied) {
   $locked=MembershipDuesPayment::lockForUpdate()->find($payment->id);
   if(!$locked || $locked->status==='success') return;
   $account=MembershipDuesAccount::lockForUpdate()->find($locked->dues_account_id);
   $start=($account->paid_through && $account->paid_through->gte(today()))?$account->paid_through->copy()->addDay():$this->currentCycleStart($account, $locked->period_type);
   $end=$locked->period_type==='annual'?$start->copy()->addYear()->subDay():$start->copy()->addMonthNoOverflow()->subDay();
   $locked->update(['status'=>'success','paid_at'=>now(),'period_start'=>$start,'period_end'=>$end,'gateway_reference'=>$data['id']??null,'gateway_response'=>$data]);
   $account->update(['plan'=>$locked->period_type,'paid_through'=>$end,'next_due_at'=>$end->copy()->addDay()]);
   MembershipDuesAuditLog::create(['user_id'=>$locked->user_id,'action'=>'dues_payment_success','details'=>['payment_id'=>$locked->id,'amount_kobo'=>$locked->amount_kobo]]);
   $applied=true;
  });

  // Send only after the transaction commits; repeated callbacks/webhooks cannot send duplicate confirmations.
  if($applied) {
   $member = \App\Models\User::find($payment->user_id);
   if($member) {
    $amount=number_format($payment->amount_kobo/100,2);
    $member->notify(new BbhsPlatformNotification(
     'BBHS membership dues payment confirmed',
     'We have successfully received your membership dues payment of ₦'.$amount.'. Your dues record has been updated. Thank you.',
     'membership',
     '/dues',
     ['payment_id'=>$payment->id,'reference'=>$payment->reference,'amount_kobo'=>$payment->amount_kobo]
    ));
   }
  }
 }
}
