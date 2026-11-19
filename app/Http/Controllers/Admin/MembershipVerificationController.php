<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GraduationSet;
use App\Models\LeadershipAssignment;
use App\Models\MemberBranch;
use App\Models\MembershipAuditLog;
use App\Models\MembershipVerification;
use App\Notifications\BbhsPlatformNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class MembershipVerificationController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = MembershipVerification::query()
            ->with(['user.alumniProfile.graduationSet'])
            ->where('status', 'pending');

        if (! $user->isAdmin() && ! $user->hasActiveGlobalExecutiveAssignment()) {
            $assignments = $this->activeAssignments($user->id);
            $allowed = $assignments->map(fn ($a) => [$a->scope_type, (int) $a->scope_id])->all();

            $query->where(function ($outer) use ($allowed) {
                foreach ($allowed as [$type, $id]) {
                    $outer->orWhere(function ($q) use ($type, $id) {
                        $q->where('scope_type', $type)->where('scope_id', $id);
                    });
                }
            });
        }

        if ($request->filled('scope_type') && in_array($request->query('scope_type'), ['set', 'branch'], true)) {
            $query->where('scope_type', $request->query('scope_type'));
        }

        if ($request->filled('scope_id')) {
            $query->where('scope_id', (int) $request->query('scope_id'));
        }

        $items = $query->latest()->paginate(25)->withQueryString();

        $items->getCollection()->transform(function ($item) {
            $item->scope_name = match ($item->scope_type) {
                'set' => GraduationSet::whereKey($item->scope_id)->value('name') ?? 'Unknown set',
                'branch' => Branch::whereKey($item->scope_id)->value('name') ?? 'Unknown branch',
                default => 'Unknown scope',
            };
            return $item;
        });

        $scopes = ($user->isAdmin() || $user->hasActiveGlobalExecutiveAssignment())
            ? [
                'sets' => GraduationSet::orderBy('graduation_year')->get(['id', 'name']),
                'branches' => Branch::orderBy('name')->get(['id', 'name']),
            ]
            : $this->scopesForAssignments($this->activeAssignments($user->id));

        return Inertia::render('Admin/Verifications/Index', [
            'verifications' => $items,
            'scopes' => $scopes,
            'filters' => $request->only(['scope_type', 'scope_id']),
            'isGlobalAdmin' => $user->isAdmin(),
            'reminderGroups' => $user->isAdmin() ? $this->reminderGroups() : [],
        ]);
    }

    /** Send a reminder to active executives responsible for one pending scope. */
    public function remind(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403, 'Only the Super Admin can send verification reminders.');

        $validated = $request->validate([
            'scope_type' => ['required', 'in:set,branch'],
            'scope_id' => ['required', 'integer', 'min:1'],
        ]);

        $scopeType = $validated['scope_type'];
        $scopeId = (int) $validated['scope_id'];

        $pending = MembershipVerification::query()
            ->with('user:id,name,email')
            ->where('status', 'pending')
            ->where('scope_type', $scopeType)
            ->where('scope_id', $scopeId)
            ->get();

        if ($pending->isEmpty()) {
            return back()->with('error', 'There are no pending registrations in this queue.');
        }

        $recentReminder = MembershipAuditLog::query()
            ->where('action', 'verification_reminder_sent')
            ->where('scope_type', $scopeType)
            ->where('scope_id', $scopeId)
            ->where('created_at', '>=', now()->subHours(24))
            ->exists();

        if ($recentReminder) {
            return back()->with('error', 'A reminder was already sent for this queue within the last 24 hours.');
        }

        $assignments = LeadershipAssignment::query()
            ->with('user:id,name,email')
            ->where('scope_type', $scopeType)
            ->where('scope_id', $scopeId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhereDate('starts_at', '<=', now()->toDateString());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhereDate('ends_at', '>=', now()->toDateString());
            })
            ->get();

        $recipients = $assignments->pluck('user')->filter(fn ($user) => $user && $user->email)
            ->unique('id')->values();

        if ($recipients->isEmpty()) {
            return back()->with('error', 'No active Set or Branch executives with email addresses are assigned to this queue.');
        }

        $scopeName = $scopeType === 'set'
            ? (GraduationSet::whereKey($scopeId)->value('name') ?? 'Graduation Set')
            : (Branch::whereKey($scopeId)->value('name') ?? 'Branch');
        $queueLabel = $scopeType === 'set' ? 'Graduation Set' : 'Branch';
        $applicantLines = $pending->map(fn ($item) => '- '.($item->user?->name ?? 'Applicant').' ('.($item->user?->email ?? 'No email').')')->implode("\n");
        $sent = 0;
        $failed = 0;
        $notified = 0;

        foreach ($recipients as $recipient) {
            // Always create the in-app alert, even if the email transport is unavailable.
            $recipient->notify(new BbhsPlatformNotification(
                'Membership reviews pending',
                $pending->count() . ' membership verification record(s) are awaiting review for ' . $scopeName . '.',
                'membership',
                '/admin/verifications',
                ['scope_type' => $scopeType, 'scope_id' => $scopeId, 'pending_count' => $pending->count(), 'database_only' => true]
            ));
            $notified++;

            try {
                Mail::raw(
                    "Dear {$recipient->name},\n\nThis is a reminder that {$pending->count()} membership registration(s) are awaiting review for {$scopeName} ({$queueLabel}).\n\nPlease sign in to the BBHS members portal and open the Membership Verifications page to review the pending queue.\n\nPending applicants:\n{$applicantLines}\n\nThis is an administrative reminder only. No application status has been changed.\n\nBBHS Membership Administration",
                    function ($message) use ($recipient, $scopeName) {
                        $message->to($recipient->email, $recipient->name)
                            ->subject("BBHS reminder: pending registrations for {$scopeName}");
                    }
                );
                $sent++;
            } catch (\Throwable $exception) {
                $failed++;
                Log::error('BBHS verification reminder email failed.', [
                    'recipient' => $recipient->email,
                    'scope_type' => $scopeType,
                    'scope_id' => $scopeId,
                    'error' => $exception->getMessage(),
                ]);
            }
        }

        if ($notified > 0) {
            DB::transaction(function () use ($pending, $request, $scopeType, $scopeId, $sent, $failed, $notified, $recipients) {
                foreach ($pending as $item) {
                    MembershipAuditLog::create([
                        'user_id' => $item->user_id,
                        'actor_id' => $request->user()->id,
                        'action' => 'verification_reminder_sent',
                        'scope_type' => $scopeType,
                        'scope_id' => $scopeId,
                        'details' => [
                            'verification_id' => $item->id,
                            'recipient_count' => $notified,
                            'email_sent_count' => $sent,
                            'failed_recipient_count' => $failed,
                            'recipient_emails' => $recipients->pluck('email')->values()->all(),
                        ],
                        'ip_address' => $request->ip(),
                    ]);
                }
            });
        }

        if ($notified === 0) {
            return back()->with('error', 'The in-app reminder could not be saved. Check the application logs.');
        }

        $message = "In-app reminder sent to {$notified} executive(s) for {$scopeName}. {$pending->count()} pending registration(s) were included.";
        if ($sent > 0) {
            $message .= " {$sent} email reminder(s) also sent.";
        }
        if ($failed > 0) {
            $message .= " {$failed} email(s) failed; check the application log.";
        }

        return back()->with('success', $message);
    }

    private function reminderGroups(): array
    {
        $pendingScopes = MembershipVerification::query()
            ->select('scope_type', 'scope_id', DB::raw('COUNT(*) as pending_count'))
            ->where('status', 'pending')
            ->groupBy('scope_type', 'scope_id')
            ->get();

        return $pendingScopes->map(function ($scope) {
            $type = $scope->scope_type;
            $id = (int) $scope->scope_id;
            $assignments = LeadershipAssignment::query()
                ->with('user:id,name,email')
                ->where('scope_type', $type)
                ->where('scope_id', $id)
                ->where('is_active', true)
                ->where(function ($query) {
                    $query->whereNull('starts_at')->orWhereDate('starts_at', '<=', now()->toDateString());
                })
                ->where(function ($query) {
                    $query->whereNull('ends_at')->orWhereDate('ends_at', '>=', now()->toDateString());
                })
                ->get();
            $recipients = $assignments->pluck('user')->filter(fn ($user) => $user && $user->email)->unique('id')->values();
            $lastReminder = MembershipAuditLog::query()
                ->where('action', 'verification_reminder_sent')
                ->where('scope_type', $type)
                ->where('scope_id', $id)
                ->latest()
                ->value('created_at');

            return [
                'scope_type' => $type,
                'scope_id' => $id,
                'scope_name' => $type === 'set'
                    ? (GraduationSet::whereKey($id)->value('name') ?? 'Unknown set')
                    : (Branch::whereKey($id)->value('name') ?? 'Unknown branch'),
                'pending_count' => (int) $scope->pending_count,
                'recipient_count' => $recipients->count(),
                'recipient_names' => $recipients->pluck('name')->values(),
                'last_reminder_at' => $lastReminder,
                'cooldown_active' => $lastReminder && \Illuminate\Support\Carbon::parse($lastReminder)->greaterThan(now()->subHours(24)),
            ];
        })->values()->all();
    }

    public function approve(Request $request, MembershipVerification $verification)
    {
        $this->ensureCanReview($request, $verification);

        abort_unless($verification->status === 'pending', 422, 'This verification has already been reviewed.');

        DB::transaction(function () use ($request, $verification) {
            $verification->update([
                'status' => 'approved',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'review_notes' => $request->input('review_notes'),
            ]);

            if ($verification->verification_type === 'set') {
                $verification->user->forceFill(['member_status' => 'approved'])->save();
                $verification->user->alumniProfile()->updateOrCreate(
                    ['user_id' => $verification->user_id],
                    ['approved' => true, 'directory_visible' => true]
                );
                $action = 'set_membership_approved';
            } else {
                MemberBranch::where('user_id', $verification->user_id)
                    ->where('branch_id', $verification->scope_id)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'approved',
                        'reviewed_at' => now(),
                        'reviewed_by' => $request->user()->id,
                        'review_notes' => $request->input('review_notes'),
                    ]);
                $action = 'branch_verification_approved';
            }

            MembershipAuditLog::create([
                'user_id' => $verification->user_id,
                'actor_id' => $request->user()->id,
                'action' => $action,
                'scope_type' => $verification->scope_type,
                'scope_id' => $verification->scope_id,
                'details' => ['verification_id' => $verification->id, 'notes' => $request->input('review_notes')],
                'ip_address' => $request->ip(),
            ]);
        });

        $verification->user?->notify(new BbhsPlatformNotification(
            $verification->verification_type === 'set' ? 'Membership approved' : 'Branch verification completed',
            $verification->verification_type === 'set'
                ? 'Your BBHS graduation set membership has been approved. You are now eligible to appear in the alumni directory.'
                : 'Your local branch verification has been completed. This does not automatically change your branch affiliation.',
            'membership',
            '/dashboard',
            ['verification_id' => $verification->id, 'status' => 'approved']
        ));

        return back()->with('success', $verification->verification_type === 'set'
            ? 'Set membership approved. The member is now eligible to appear in the alumni directory.'
            : 'Branch verification completed. This does not change the member’s optional branch affiliation.');
    }

    public function reject(Request $request, MembershipVerification $verification)
    {
        $validated = $request->validate([
            'review_notes' => ['required', 'string', 'max:2000'],
        ]);

        $this->ensureCanReview($request, $verification);

        abort_unless($verification->status === 'pending', 422, 'This verification has already been reviewed.');

        DB::transaction(function () use ($request, $verification, $validated) {
            $verification->update([
                'status' => 'rejected',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
                'review_notes' => $validated['review_notes'],
            ]);

            if ($verification->verification_type === 'set') {
                $verification->user->forceFill(['member_status' => 'rejected'])->save();
                $verification->user->alumniProfile()->updateOrCreate(
                    ['user_id' => $verification->user_id],
                    ['approved' => false, 'directory_visible' => false]
                );
                $action = 'set_membership_rejected';
            } else {
                MemberBranch::where('user_id', $verification->user_id)
                    ->where('branch_id', $verification->scope_id)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'rejected',
                        'reviewed_at' => now(),
                        'reviewed_by' => $request->user()->id,
                        'review_notes' => $validated['review_notes'],
                    ]);
                $action = 'branch_verification_rejected';
            }

            MembershipAuditLog::create([
                'user_id' => $verification->user_id,
                'actor_id' => $request->user()->id,
                'action' => $action,
                'scope_type' => $verification->scope_type,
                'scope_id' => $verification->scope_id,
                'details' => ['verification_id' => $verification->id, 'notes' => $validated['review_notes']],
                'ip_address' => $request->ip(),
            ]);
        });

        $verification->user?->notify(new BbhsPlatformNotification(
            $verification->verification_type === 'set' ? 'Membership application update' : 'Branch verification update',
            $verification->verification_type === 'set'
                ? 'Your BBHS graduation set membership application was not approved. Please contact the association for further information.'
                : 'Your local branch verification was not approved. Please contact the relevant branch executives for further information.',
            'membership',
            '/dashboard',
            ['verification_id' => $verification->id, 'status' => 'rejected']
        ));

        return back()->with('success', 'Verification rejected and the decision has been recorded.');
    }

    private function ensureCanReview(Request $request, MembershipVerification $verification): void
    {
        $user = $request->user();

        if ($user->isAdmin() || $user->hasActiveGlobalExecutiveAssignment()) {
            return;
        }

        $allowed = $this->activeAssignments($user->id)
            ->contains(fn ($assignment) =>
                $assignment->scope_type === $verification->scope_type
                && (int) $assignment->scope_id === (int) $verification->scope_id
            );

        abort_unless($allowed, 403, 'You are not authorised to review this set or branch.');
    }

    private function activeAssignments(int $userId)
    {
        return LeadershipAssignment::query()
            ->where('user_id', $userId)
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')->orWhereDate('starts_at', '<=', now()->toDateString());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')->orWhereDate('ends_at', '>=', now()->toDateString());
            })
            ->get(['scope_type', 'scope_id']);
    }

    private function scopesForAssignments($assignments): array
    {
        $setIds = $assignments->where('scope_type', 'set')->pluck('scope_id')->filter()->unique();
        $branchIds = $assignments->where('scope_type', 'branch')->pluck('scope_id')->filter()->unique();

        return [
            'sets' => GraduationSet::whereIn('id', $setIds)->orderBy('graduation_year')->get(['id', 'name']),
            'branches' => Branch::whereIn('id', $branchIds)->orderBy('name')->get(['id', 'name']),
        ];
    }
}
