<?php

namespace App\Http\Controllers\Admin;

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
            ],
        ]);
    }

    public function show(WelfareApplication $application)
    {
        $application->load(['user.alumniProfile', 'attachments', 'disbursements', 'reviewer:id,name']);
        return Inertia::render('Admin/Welfare/Show', ['application' => $application]);
    }

    public function update(Request $request, WelfareApplication $application)
    {
        $data = $request->validate([
            'status' => ['required', 'in:under_review,more_information,approved,rejected,closed'],
            'amount_approved' => ['nullable', 'numeric', 'min:0', 'max:999999999'],
            'admin_notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $application->update([
            ...$data,
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'Welfare application updated.');
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
            $application->disbursements()->create([...$data, 'recorded_by' => $request->user()->id]);
            $paid = (float) $application->disbursements()->sum('amount');
            if ($application->amount_approved !== null && $paid >= (float) $application->amount_approved) {
                $application->update(['status' => 'closed']);
            }
        });

        return back()->with('success', 'Disbursement recorded.');
    }

    public function downloadAttachment(WelfareApplication $application, \App\Models\WelfareAttachment $attachment)
    {
        abort_unless($attachment->welfare_application_id === $application->id, 404);
        abort_unless(\Illuminate\Support\Facades\Storage::disk('local')->exists($attachment->storage_path), 404);
        return \Illuminate\Support\Facades\Storage::disk('local')->download($attachment->storage_path, $attachment->original_name);
    }
}
