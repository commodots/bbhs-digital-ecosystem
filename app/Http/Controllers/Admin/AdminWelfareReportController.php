<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WelfareApplication;
use App\Models\WelfareDisbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminWelfareReportController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $from = $filters['from'] ?? now()->startOfYear()->toDateString();
        $to = $filters['to'] ?? today()->toDateString();

        $applications = WelfareApplication::query()
            ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to]);

        $disbursements = WelfareDisbursement::query()
            ->whereBetween('disbursed_on', [$from, $to]);

        $statusCounts = (clone $applications)
            ->select('status', DB::raw('COUNT(*) as total'))
            ->groupBy('status')->pluck('total', 'status');

        $categoryCounts = (clone $applications)
            ->select('category', DB::raw('COUNT(*) as total'))
            ->groupBy('category')->orderByDesc('total')->get();

        $monthly = [];
        $cursor = \Carbon\Carbon::parse($from)->startOfMonth();
        $last = \Carbon\Carbon::parse($to)->startOfMonth();
        while ($cursor->lte($last)) {
            $start = $cursor->copy()->startOfMonth()->toDateString();
            $end = $cursor->copy()->endOfMonth()->toDateString();
            $monthly[] = [
                'month' => $cursor->format('M Y'),
                'applications' => WelfareApplication::whereBetween(DB::raw('DATE(created_at)'), [$start, $end])->count(),
                'disbursed' => (float) WelfareDisbursement::whereBetween('disbursed_on', [$start, $end])->sum('amount'),
            ];
            $cursor->addMonth();
        }

        $approved = (float) WelfareApplication::whereIn('status', ['approved', 'closed'])->sum('amount_approved');
        $allDisbursed = (float) WelfareDisbursement::sum('amount');

        return Inertia::render('Admin/WelfareReports/Index', [
            'filters' => ['from' => $from, 'to' => $to],
            'summary' => [
                'applications' => (clone $applications)->count(),
                'pending' => (int) (($statusCounts['submitted'] ?? 0) + ($statusCounts['under_review'] ?? 0) + ($statusCounts['more_information'] ?? 0)),
                'approved' => (int) ($statusCounts['approved'] ?? 0),
                'rejected' => (int) ($statusCounts['rejected'] ?? 0),
                'requested' => (float) (clone $applications)->sum('amount_requested'),
                'approved_amount_all_time' => $approved,
                'disbursed_in_period' => (float) (clone $disbursements)->sum('amount'),
                'disbursed_all_time' => $allDisbursed,
                'outstanding_all_time' => max(0, $approved - $allDisbursed),
            ],
            'statuses' => collect(['submitted', 'under_review', 'more_information', 'approved', 'rejected', 'closed'])
                ->map(fn ($status) => ['status' => $status, 'total' => (int) ($statusCounts[$status] ?? 0)])->values(),
            'categories' => $categoryCounts,
            'monthly' => $monthly,
        ]);
    }

    public function export(Request $request)
    {
        $data = $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ]);
        $from = $data['from'] ?? now()->startOfYear()->toDateString();
        $to = $data['to'] ?? today()->toDateString();

        // Global Executives receive aggregate-only exports; case-level details remain restricted.
        $actor = $request->user();
        if ($actor->hasActiveGlobalExecutiveAssignment() && ! $actor->isAdmin() && ! $actor->welfare_team) {
            return response()->streamDownload(function () use ($from, $to) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['BBHS Welfare Summary Report']);
                fputcsv($out, ['Period From', $from, 'Period To', $to]);
                fputcsv($out, []);
                fputcsv($out, ['Applications by Status', 'Count']);
                WelfareApplication::query()
                    ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
                    ->select('status', DB::raw('COUNT(*) as total'))
                    ->groupBy('status')->orderBy('status')->get()
                    ->each(fn ($row) => fputcsv($out, [$row->status, (int) $row->total]));
                fputcsv($out, []);
                fputcsv($out, ['Applications by Category', 'Count']);
                WelfareApplication::query()
                    ->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
                    ->select('category', DB::raw('COUNT(*) as total'))
                    ->groupBy('category')->orderBy('category')->get()
                    ->each(fn ($row) => fputcsv($out, [$row->category, (int) $row->total]));
                fputcsv($out, []);
                fputcsv($out, ['Financial Summary', 'Amount']);
                fputcsv($out, ['Amount Requested in Period', WelfareApplication::whereBetween(DB::raw('DATE(created_at)'), [$from, $to])->sum('amount_requested')]);
                fputcsv($out, ['Amount Disbursed in Period', WelfareDisbursement::whereBetween('disbursed_on', [$from, $to])->sum('amount')]);
                fclose($out);
            }, 'bbhs-welfare-summary-' . $from . '-to-' . $to . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        return response()->streamDownload(function () use ($from, $to) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Application Reference', 'Member', 'Category', 'Subject', 'Date Submitted', 'Status', 'Amount Requested', 'Amount Approved', 'Total Disbursed', 'Outstanding Approved']);
            WelfareApplication::with('user:id,name,email')->whereBetween(DB::raw('DATE(created_at)'), [$from, $to])
                ->orderBy('created_at')->chunk(300, function ($items) use ($out) {
                    foreach ($items as $item) {
                        $disbursed = (float) $item->disbursements()->sum('amount');
                        fputcsv($out, [
                            $item->reference, $item->user?->name, $item->category, $item->subject,
                            $item->created_at?->toDateTimeString(), $item->status,
                            $item->amount_requested, $item->amount_approved, $disbursed,
                            max(0, (float) $item->amount_approved - $disbursed),
                        ]);
                    }
                });
            fclose($out);
        }, 'bbhs-welfare-report-' . $from . '-to-' . $to . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
