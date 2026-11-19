<?php

namespace App\Http\Controllers\Admin;

use App\Notifications\BbhsPlatformNotification;

use App\Models\WelfareActivityLog;

use App\Http\Controllers\Controller;
use App\Models\WelfareApplication;
use App\Models\WelfareDisbursement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminWelfareController extends Controller
{
    public function index(Request $request)
    {
        $query = WelfareApplication::with('user:id,name,email,member_status')
            ->withSum('disbursements', 'amount')->latest();

        if ($request->filled('status')) $query->where('status', $request->string('status'));
        if ($request->filled('category')) $query->where('category', $request->string('category'));

        $applications = $query->paginate(20)->withQueryString();

        return Inertia::render('Admin/Welfare/Index', [
            'applications' => $applications,
            'filters' => $request->only('status', 'category'),
            'stats' => [
                'total' => WelfareApplication::count(),
                'pending' => WelfareApplication::whereIn('status', ['submitted', 'under_review', 'more_information'])->count(),
                'approved' => WelfareApplication::where('status', 'approved')->count(),
                'disbursed' => WelfareDisbursement::sum('amount'),
                'approved_amount' => WelfareApplication::whereIn('status', ['approved', 'closed'])->sum('amount_approved'),
                'outstanding' => max(0, (float) WelfareApplication::whereIn('status', ['approved', 'closed'])->sum('amount_approved') - (float) WelfareDisbursement::sum('amount')),
            ],
        ]);
    }

    public function show(WelfareApplication $application)
    {
        $application->load(['user.alumniProfile', 'attachments', 'disbursements', 'reviewer:id,name', 'activityLogs.actor:id,name']);
        return Inertia::render('Admin/Welfare/Show', ['application' => $application]);
    }

    public function update(Request $request, WelfareApplication $application)
    {
        $data = $request->validate([
            'status' => ['required', 'in:under_review,more_information,approved,rejected,closed'],
            'amount_approved' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'admin_notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $before = $application->only(['status', 'amount_approved']);
        $application->update([
            ...$data,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);
        WelfareActivityLog::create([
            'welfare_application_id' => $application->id,
            'actor_id' => $request->user()->id,
            'action' => 'application_reviewed',
            'details' => ['before' => $before, 'after' => $application->only(['status', 'amount_approved'])],
        ]);
        $application->user?->notify(new BbhsPlatformNotification(
            'Welfare application update',
            'Your welfare application '.$application->reference.' has been updated. Current status: '.str_replace('_', ' ', $application->status).'.',
            'welfare', '/welfare/'.$application->id,
            ['application_id' => $application->id, 'reference' => $application->reference, 'status' => $application->status]
        ));

        return back()->with('success', 'Welfare application updated and the applicant has been notified.');
    }

    public function disburse(Request $request, WelfareApplication $application)
    {
        abort_unless($application->status === 'approved', 422, 'Only approved applications can be disbursed.');
        $data = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0'],
            'disbursed_on' => ['required', 'date'],
            'method' => ['nullable', 'string', 'max:100'],
            'reference' => ['nullable', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $total = (float) $application->disbursements()->sum('amount') + (float) $data['amount'];
        abort_if($application->amount_approved !== null && $total > (float) $application->amount_approved, 422, 'Disbursement exceeds the approved amount.');

        DB::transaction(function () use ($application, $data, $request) {
            $disbursement = $application->disbursements()->create([...$data, 'recorded_by' => $request->user()->id]);
            WelfareActivityLog::create([
                'welfare_application_id' => $application->id,
                'actor_id' => $request->user()->id,
                'action' => 'disbursement_recorded',
                'details' => ['disbursement_id' => $disbursement->id, 'amount' => $data['amount'], 'reference' => $data['reference'] ?? null],
            ]);
            $paid = (float) $application->disbursements()->sum('amount');
            if ($application->amount_approved !== null && $paid >= (float) $application->amount_approved) {
                $application->update(['status' => 'closed']);
            }
        });
        $application->user?->notify(new BbhsPlatformNotification(
            'Welfare assistance disbursement recorded',
            'A disbursement of ₦'.number_format((float) $data['amount'], 2).' has been recorded for your welfare application '.$application->reference.'.',
            'welfare', '/welfare/'.$application->id,
            ['application_id' => $application->id, 'reference' => $application->reference, 'amount' => $data['amount']]
        ));

        return back()->with('success', 'Disbursement recorded and the applicant has been notified.');
    }

    public function downloadAttachment(WelfareApplication $application, \App\Models\WelfareAttachment $attachment)
    {
        abort_unless($attachment->welfare_application_id === $application->id, 404);
        abort_unless(\Illuminate\Support\Facades\Storage::disk('local')->exists($attachment->storage_path), 404);
        return \Illuminate\Support\Facades\Storage::disk('local')->download($attachment->storage_path, $attachment->original_name);
    }
}
