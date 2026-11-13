<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FundraisingDonation;
use App\Models\FundraisingPledge;
use App\Models\MembershipDuesPayment;
use App\Models\WelfareDisbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminFinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);

        $from = $data['from'] ?? now()->startOfYear()->toDateString();
        $to = $data['to'] ?? today()->toDateString();

        $dues = MembershipDuesPayment::where('status', 'success')
            ->whereBetween(DB::raw('DATE(COALESCE(paid_at, created_at))'), [$from, $to]);
        $donations = FundraisingDonation::where('status', 'success')
            ->whereBetween(DB::raw('DATE(COALESCE(paid_at, created_at))'), [$from, $to]);
        $welfare = WelfareDisbursement::whereBetween('disbursed_on', [$from, $to]);

        $duesTotal = (int) (clone $dues)->sum('amount_kobo');
        $donationsTotal = (int) (clone $donations)->sum('amount_kobo');
        $welfareTotal = (float) (clone $welfare)->sum('amount');

        $pledges = FundraisingPledge::whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);
        $pledgeTotal = (int) (clone $pledges)->sum('amount_kobo');

        $monthly = [];
        $cursor = \Carbon\Carbon::parse($from)->startOfMonth();
        $last = \Carbon\Carbon::parse($to)->startOfMonth();
        while ($cursor->lte($last)) {
            $start = $cursor->copy()->startOfMonth()->toDateString();
            $end = $cursor->copy()->endOfMonth()->toDateString();
            $monthly[] = [
                'month' => $cursor->format('M Y'),
                'dues' => (int) MembershipDuesPayment::where('status','success')->whereBetween(DB::raw('DATE(COALESCE(paid_at, created_at))'), [$start,$end])->sum('amount_kobo') / 100,
                'donations' => (int) FundraisingDonation::where('status','success')->whereBetween(DB::raw('DATE(COALESCE(paid_at, created_at))'), [$start,$end])->sum('amount_kobo') / 100,
                'welfare' => (float) WelfareDisbursement::whereBetween('disbursed_on', [$start,$end])->sum('amount'),
            ];
            $cursor->addMonth();
        }

        $campaigns = DB::table('fundraising_campaigns as c')
            ->leftJoin('fundraising_donations as d', function ($join) {
                $join->on('d.campaign_id','=','c.id')->where('d.status','=','success');
            })
            ->select('c.id','c.title','c.scope_type','c.status','c.target_amount_kobo',
                DB::raw('COALESCE(SUM(d.amount_kobo),0) as raised_kobo'),
                DB::raw('COUNT(d.id) as donation_count'))
            ->groupBy('c.id','c.title','c.scope_type','c.status','c.target_amount_kobo')
            ->orderByDesc('raised_kobo')->limit(15)->get()
            ->map(fn($c) => [
                'id'=>$c->id,'title'=>$c->title,'scope_type'=>$c->scope_type,'status'=>$c->status,
                'target'=>$c->target_amount_kobo/100,'raised'=>$c->raised_kobo/100,
                'donations'=>(int)$c->donation_count,
            ]);

        $duesStatus = DB::table('membership_dues_accounts')
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("SUM(CASE WHEN exempt = 1 THEN 1 ELSE 0 END) as exempt")
            ->selectRaw("SUM(CASE WHEN exempt = 0 AND paid_through >= ? THEN 1 ELSE 0 END) as up_to_date", [today()->toDateString()])
            ->selectRaw("SUM(CASE WHEN exempt = 0 AND (paid_through IS NULL OR paid_through < ?) THEN 1 ELSE 0 END) as outstanding", [today()->toDateString()])
            ->first();

        $pending = [
            'dues' => MembershipDuesPayment::where('status','pending')->count(),
            'donations' => FundraisingDonation::where('status','pending')->count(),
            'pledges_open' => FundraisingPledge::whereIn('status',['open','pending'])->count(),
        ];

        return Inertia::render('Admin/FinancialReports/Index', [
            'filters' => ['from'=>$from,'to'=>$to],
            'totals' => [
                'dues_collected'=>$duesTotal/100,
                'donations_received'=>$donationsTotal/100,
                'welfare_disbursed'=>$welfareTotal,
                'pledges_recorded'=>$pledgeTotal/100,
                'total_income'=>($duesTotal+$donationsTotal)/100,
                'net_after_welfare'=>(($duesTotal+$donationsTotal)/100)-$welfareTotal,
            ],
            'duesStatus' => [
                'total'=>(int)($duesStatus->total ?? 0),
                'up_to_date'=>(int)($duesStatus->up_to_date ?? 0),
                'outstanding'=>(int)($duesStatus->outstanding ?? 0),
                'exempt'=>(int)($duesStatus->exempt ?? 0),
            ],
            'pending'=>$pending,
            'monthly'=>$monthly,
            'campaigns'=>$campaigns,
        ]);
    }

    public function export(Request $request)
    {
        $data = $request->validate([
            'from' => ['nullable','date'],
            'to' => ['nullable','date','after_or_equal:from'],
        ]);
        $from = $data['from'] ?? now()->startOfYear()->toDateString();
        $to = $data['to'] ?? today()->toDateString();

        $rows = [];
        MembershipDuesPayment::with('user:id,name,email')->where('status','success')
            ->whereBetween(DB::raw('DATE(COALESCE(paid_at, created_at))'),[$from,$to])->get()
            ->each(fn($p)=>$rows[]=['Membership dues',$p->paid_at?->toDateTimeString() ?? $p->created_at?->toDateTimeString(),$p->user?->name,$p->user?->email,$p->reference,$p->amount_kobo/100,'NGN','Successful']);
        FundraisingDonation::with('campaign:id,title','user:id,name,email')->where('status','success')
            ->whereBetween(DB::raw('DATE(COALESCE(paid_at, created_at))'),[$from,$to])->get()
            ->each(fn($p)=>$rows[]=['Donation: '.$p->campaign?->title,$p->paid_at?->toDateTimeString() ?? $p->created_at?->toDateTimeString(),$p->is_anonymous?'Anonymous':($p->user?->name ?? $p->donor_name),$p->is_anonymous?'':($p->user?->email ?? $p->donor_email),$p->reference,$p->amount_kobo/100,'NGN','Successful']);
        WelfareDisbursement::with('application.user:id,name,email')->whereBetween('disbursed_on',[$from,$to])->get()
            ->each(fn($p)=>$rows[]=['Welfare disbursement',$p->disbursed_on?->toDateString(),$p->application?->user?->name,$p->application?->user?->email,$p->reference,$p->amount,'NGN','Disbursed']);

        $filename = 'bbhs-financial-report-'.$from.'-to-'.$to.'.csv';
        return response()->streamDownload(function () use ($rows) {
            $out=fopen('php://output','w');
            fputcsv($out,['Category','Date','Member/Donor','Email','Reference','Amount','Currency','Status']);
            foreach($rows as $row) fputcsv($out,$row);
            fclose($out);
        },$filename,['Content-Type'=>'text/csv; charset=UTF-8']);
    }
}
