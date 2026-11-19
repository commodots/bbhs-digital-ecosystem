<?php
namespace App\Http\Controllers\Admin;

use App\Support\MembershipDuesCycle;
use App\Http\Controllers\Controller;
use App\Models\MembershipDuesAccount;
use App\Models\MembershipDuesPayment;
use App\Models\MembershipDuesAuditLog;
use App\Models\User;
use App\Notifications\BbhsPlatformNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
class AdminMembershipDuesController extends Controller {
 public function index(Request $request) {
  User::query()->where('role','member')->orWhereNull('role')->select('id','created_at')->chunkById(200,function($members){ foreach($members as $member) MembershipDuesAccount::firstOrCreate(['user_id'=>$member->id],['commencement_date'=>$member->created_at?->toDateString()??today(),'plan'=>'annual','annual_amount_kobo'=>1250000,'monthly_amount_kobo'=>105000,'next_due_at'=>$member->created_at?->toDateString()??today()]); });
  $q=MembershipDuesAccount::with('user:id,name,email,member_status')->withCount(['payments as successful_payments_count'=>fn($x)=>$x->where('status','success')]);
  if($request->filled('status')) { $status=$request->string('status')->toString(); if($status==='exempt') $q->where('exempt',true); elseif($status==='up_to_date') $q->where('paid_through','>=',today())->where('exempt',false); elseif($status==='overdue') $q->where('next_due_at','<',today())->whereNull('paid_through')->where('exempt',false); elseif($status==='due') $q->where(fn($x)=>$x->whereNull('paid_through')->orWhere('paid_through','<',today()))->where('exempt',false); }
  if($request->filled('search')) $q->whereHas('user',fn($x)=>$x->where('name','like','%'.$request->search.'%')->orWhere('email','like','%'.$request->search.'%'));
  $accounts=$q->latest()->paginate(25)->withQueryString()->through(fn($a)=>['id'=>$a->id,'user_id'=>$a->user_id,'name'=>$a->user?->name,'email'=>$a->user?->email,'member_status'=>$a->user?->member_status,'plan'=>$a->plan,'commencement_date'=>$a->commencement_date?->format('Y-m-d'),'paid_through'=>$a->paid_through?->format('Y-m-d'),'next_due_at'=>$a->next_due_at?->format('Y-m-d'),'status'=>$a->computed_status,'exempt'=>$a->exempt]);
  return Inertia::render('Admin/Dues/Index',['accounts'=>$accounts,'filters'=>$request->only('status','search'),'stats'=>['members'=>MembershipDuesAccount::count(),'up_to_date'=>MembershipDuesAccount::where('paid_through','>=',today())->where('exempt',false)->count(),'overdue'=>MembershipDuesAccount::where('next_due_at','<',today())->where('exempt',false)->count(),'exempt'=>MembershipDuesAccount::where('exempt',true)->count(),'collected'=>MembershipDuesPayment::where('status','success')->sum('amount_kobo')/100]]);
 }
 public function payments(Request $request) {
  $query=MembershipDuesPayment::with('user:id,name,email')->latest();
  if($request->filled('status')) $query->where('status',$request->string('status')->toString());
  if($request->filled('method')) $query->where('method',$request->string('method')->toString());
  if($request->filled('search')) $query->where(fn($q)=>$q->where('reference','like','%'.$request->search.'%')->orWhereHas('user',fn($u)=>$u->where('name','like','%'.$request->search.'%')->orWhere('email','like','%'.$request->search.'%')));
  $payments=$query->paginate(30)->withQueryString()->through(fn($p)=>[
   'id'=>$p->id,'user_id'=>$p->user_id,'member'=>$p->user?->name,'email'=>$p->user?->email,
   'reference'=>$p->reference,'amount'=>$p->amount_kobo/100,'period_type'=>$p->period_type,
   'method'=>$p->method,'status'=>$p->status,'paid_at'=>$p->paid_at?->format('d M Y, h:i A'),
   'created_at'=>$p->created_at?->format('d M Y, h:i A')
  ]);
  return Inertia::render('Admin/Dues/Payments',['payments'=>$payments,'filters'=>$request->only('status','method','search'),'summary'=>[
   'successful'=>MembershipDuesPayment::where('status','success')->count(),
   'pending'=>MembershipDuesPayment::where('status','pending')->count(),
   'failed'=>MembershipDuesPayment::where('status','failed')->count(),
   'collected'=>MembershipDuesPayment::where('status','success')->sum('amount_kobo')/100
  ]]);
 }
 public function show(User $user) { $account=MembershipDuesAccount::firstOrCreate(['user_id'=>$user->id],['commencement_date'=>$user->created_at?->toDateString()??today(),'plan'=>'annual','annual_amount_kobo'=>1250000,'monthly_amount_kobo'=>105000,'next_due_at'=>$user->created_at?->toDateString()??today()]); return Inertia::render('Admin/Dues/Show',['member'=>['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'member_status'=>$user->member_status],'account'=>$account,'status'=>$account->computed_status,'payments'=>$account->payments()->latest()->get()]); }
 public function update(Request $request, User $user) {
  $data=$request->validate(['commencement_date'=>['required','date'],'plan'=>['required','in:annual,monthly'],'exempt'=>['required','boolean'],'annual_amount'=>['required','numeric','min:0'],'monthly_amount'=>['required','numeric','min:0'],'admin_note'=>['nullable','string','max:2000']]);
  $account=MembershipDuesAccount::firstOrCreate(['user_id'=>$user->id],['commencement_date'=>$user->created_at?->toDateString()??today(),'next_due_at'=>$user->created_at?->toDateString()??today()]);
  $old=$account->only(['commencement_date','plan','exempt','annual_amount_kobo','monthly_amount_kobo']);
  $account->update(['commencement_date'=>$data['commencement_date'],'plan'=>$data['plan'],'exempt'=>$data['exempt'],'annual_amount_kobo'=>(int)round($data['annual_amount']*100),'monthly_amount_kobo'=>(int)round($data['monthly_amount']*100),'admin_note'=>$data['admin_note']??null,'updated_by'=>$request->user()->id,'next_due_at'=>$account->paid_through?->copy()->addDay() ?? $data['commencement_date']]);
  MembershipDuesAuditLog::create(['user_id'=>$user->id,'actor_id'=>$request->user()->id,'action'=>'dues_account_updated','details'=>['before'=>$old,'after'=>$account->only(['commencement_date','plan','exempt','annual_amount_kobo','monthly_amount_kobo'])]]);
  return back()->with('success','Membership dues settings updated.');
 }
 public function remind(User $user, Request $request) { $account=MembershipDuesAccount::firstOrCreate(['user_id'=>$user->id],['commencement_date'=>$user->created_at?->toDateString()??today(),'next_due_at'=>$user->created_at?->toDateString()??today()]); $user->notify(new BbhsPlatformNotification('BBHS membership dues reminder','Please review your membership dues status and make payment if due.','membership','/dues')); $account->update(['last_due_notice_at'=>now()]); return back()->with('success','Dues reminder sent.'); }
 public function remindBulk(Request $request) { $data=$request->validate(['status'=>['required','in:due,overdue,all']]); $q=MembershipDuesAccount::with('user')->where('exempt',false); if($data['status']==='overdue') $q->where('next_due_at','<',today()); if($data['status']==='due') $q->where(fn($x)=>$x->whereNull('paid_through')->orWhere('paid_through','<',today())); $count=0; foreach($q->get() as $account) { if($account->user) { $account->user->notify(new BbhsPlatformNotification('BBHS membership dues reminder','Please review your membership dues status and make payment if due.','membership','/dues')); $account->update(['last_due_notice_at'=>now()]); $count++; } } return back()->with('success',"Dues reminders sent to {$count} members."); }
 public function verifyManual(Request $request, MembershipDuesPayment $payment) { $data=$request->validate(['action'=>['required','in:approve,reject'],'admin_note'=>['nullable','string','max:1000']]); abort_unless($payment->method==='bank_transfer' && $payment->status==='pending',422); if($data['action']==='reject') $payment->update(['status'=>'failed','admin_note'=>$data['admin_note']??null,'verified_by'=>$request->user()->id]); else { DB::transaction(function()use($payment,$request,$data){ $p=MembershipDuesPayment::lockForUpdate()->find($payment->id); if($p->status!=='pending') return; $a=MembershipDuesAccount::lockForUpdate()->find($p->dues_account_id); $start=MembershipDuesCycle::nextUnpaidStart($a, $p->period_type); $end=MembershipDuesCycle::periodEnd($a,$start,$p->period_type); $p->update(['status'=>'success','paid_at'=>now(),'period_start'=>$start,'period_end'=>$end,'verified_by'=>$request->user()->id,'admin_note'=>$data['admin_note']??null]); $a->update(['paid_through'=>$end,'next_due_at'=>$end->copy()->addDay()]); }); if($data['action']==='approve') { $payment->refresh(); $payment->user?->notify(new BbhsPlatformNotification('BBHS membership dues payment confirmed','Your bank transfer payment of ₦'.number_format($payment->amount_kobo/100,2).' has been verified.','membership','/dues',['payment_id'=>$payment->id,'reference'=>$payment->reference])); } } return back()->with('success','Manual payment review completed.'); }
}
