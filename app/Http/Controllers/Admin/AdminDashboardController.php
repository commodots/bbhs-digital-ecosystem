<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\News;
use App\Models\FundraisingCampaign;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $members = User::query()->where('is_admin', false);
        $pendingMembers = (clone $members)->where('member_status', 'pending')->count();

        $duesCollected = DB::table('membership_dues_payments')->where('status', 'success')->sum('amount_kobo') / 100;
        $donationsReceived = DB::table('fundraising_donations')->where('status', 'success')->sum('amount_kobo') / 100;
        $welfareDisbursed = (float) DB::table('welfare_disbursements')->sum('amount');

        $duesAccounts = DB::table('membership_dues_accounts');
        $duesOutstanding = (clone $duesAccounts)->where('exempt', false)
            ->where(function ($q) {
                $q->whereNull('paid_through')->orWhereDate('paid_through', '<', today());
            })->count();

        $pendingVerifications = DB::table('membership_verifications')->where('status', 'pending')->count();
        $pendingDuesPayments = DB::table('membership_dues_payments')->where('status', 'pending')->count();
        $pendingDonations = DB::table('fundraising_donations')->where('status', 'pending')->count();
        $pendingWelfare = DB::table('welfare_applications')
            ->whereIn('status', ['submitted', 'under_review', 'more_information'])->count();

        $bySet = DB::table('users')
            ->join('alumni_profiles', 'alumni_profiles.user_id', '=', 'users.id')
            ->leftJoin('graduation_sets', 'graduation_sets.id', '=', 'alumni_profiles.graduation_set_id')
            ->where('users.is_admin', false)
            ->selectRaw("COALESCE(graduation_sets.name, alumni_profiles.class_set, 'Not assigned') as label, COUNT(DISTINCT users.id) as total")
            ->groupBy('label')->orderByDesc('total')->limit(10)->get();

        $byBranch = DB::table('member_branches')
            ->join('branches', 'branches.id', '=', 'member_branches.branch_id')
            ->join('users', 'users.id', '=', 'member_branches.user_id')
            ->where('users.is_admin', false)
            ->whereIn('member_branches.status', ['pending', 'approved'])
            ->select('branches.name as label')
            ->selectRaw('COUNT(DISTINCT users.id) as total')
            ->groupBy('branches.id', 'branches.name')->orderByDesc('total')->limit(10)->get();

        $monthly = DB::table('users')
            ->where('is_admin', false)
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month_key, COUNT(*) as total")
            ->groupBy('month_key')->orderBy('month_key')->get()->keyBy('month_key');
        $registrationTrend = [];
        $cursor = now()->subMonths(5)->startOfMonth();
        for ($i = 0; $i < 6; $i++) {
            $key = $cursor->format('Y-m');
            $registrationTrend[] = ['month' => $cursor->format('M Y'), 'total' => (int) ($monthly[$key]->total ?? 0)];
            $cursor->addMonth();
        }

        return Inertia::render('Admin/Dashboard', [
            'admin' => $request->user(),
            'stats' => [
                'members' => (clone $members)->count(),
                'pending_members' => $pendingMembers,
                'pending_verifications' => $pendingVerifications,
                'dues_collected' => $duesCollected,
                'donations_received' => $donationsReceived,
                'welfare_disbursed' => $welfareDisbursed,
                'dues_outstanding_members' => $duesOutstanding,
                'pending_financial_items' => $pendingDuesPayments + $pendingDonations,
                'pending_welfare' => $pendingWelfare,
                'news' => News::count(),
                'published_news' => News::where('published', true)->count(),
                'events' => Event::count(),
                'upcoming_events' => Event::where('published', true)->whereNotNull('starts_at')->where('starts_at', '>=', now())->count(),
                'active_fundraising_campaigns' => FundraisingCampaign::where('published', true)->where('status', 'active')->count(),
            ],
            'bySet' => $bySet,
            'byBranch' => $byBranch,
            'registrationTrend' => $registrationTrend,
            'recentMembers' => User::where('is_admin', false)->latest()->take(5)->get(['id','name','email','member_status']),
            'upcomingEvents' => Event::where('published', true)->whereNotNull('starts_at')->where('starts_at', '>=', now())
                ->orderBy('starts_at')->take(5)->get(['id','title','starts_at','venue']),
            'recentNews' => News::latest()->take(5)->get(['id','title','published','published_at']),
        ]);
    }
}
