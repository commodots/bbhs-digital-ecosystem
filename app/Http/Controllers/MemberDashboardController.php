<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\EventRegistration;
use App\Models\MembershipDuesAccount;
use App\Notifications\BbhsPlatformNotification;

class MemberDashboardController extends Controller
{
    public function index()
    {
        $user = request()->user()->load('alumniProfile');

        // Show executive tools only to users with a currently active
        // global, graduation-set or branch leadership appointment.
        $executiveAssignments = $user->leadershipAssignments()
            ->with('position')
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhereDate('starts_at', '<=', now()->toDateString());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhereDate('ends_at', '>=', now()->toDateString());
            })
            ->get();

        $dues = MembershipDuesAccount::firstOrCreate(['user_id' => $user->id], [
            'commencement_date' => $user->created_at?->toDateString() ?? today(),
            'plan' => 'annual', 'annual_amount_kobo' => 1250000, 'monthly_amount_kobo' => 105000,
            'next_due_at' => $user->created_at?->toDateString() ?? today(), 'status' => 'due',
        ]);
        $duesStatus = $dues->computed_status;
        if (!$dues->exempt && in_array($duesStatus, ['due', 'overdue'], true) && (!$dues->last_due_notice_at || $dues->last_due_notice_at->lt(now()->subDays(7)))) {
            $user->notify(new BbhsPlatformNotification('BBHS membership dues payment', 'Your membership dues are due. Please review your status and payment options.', 'membership', '/dues'));
            $dues->update(['last_due_notice_at' => now()]);
        }

        return Inertia::render('Dashboard', [
            'duesSummary' => ['status' => $duesStatus, 'status_label' => match($duesStatus) { 'up_to_date' => 'Your dues are up to date', 'exempt' => 'Dues exemption is active', 'overdue' => 'Your membership dues are overdue', default => 'Membership dues payment is due' }, 'next_due_at' => $dues->next_due_at?->format('d M Y')],
            'member' => $user,
            'alumniProfile' => $user->alumniProfile,
            'executiveAccess' => $executiveAssignments->isNotEmpty(),
            'myUpcomingEvents' => EventRegistration::query()
                ->with('event:id,title,slug,starts_at,venue,location,image')
                ->where('user_id', $user->id)
                ->where('status', 'registered')
                ->whereHas('event', fn ($query) => $query->where('published', true)->where(function ($q) {
                    $q->whereNull('starts_at')->orWhere('starts_at', '>=', now());
                }))
                ->latest()
                ->limit(6)
                ->get()
                ->map(fn ($registration) => [
                    'id' => $registration->id,
                    'event' => $registration->event ? [
                        'title' => $registration->event->title,
                        'slug' => $registration->event->slug,
                        'starts_at' => optional($registration->event->starts_at)->format('M d, Y g:i A'),
                        'venue' => $registration->event->venue,
                        'location' => $registration->event->location,
                    ] : null,
                ])->filter(fn ($item) => $item['event'] !== null)->values(),
            'executiveAssignments' => $executiveAssignments->map(function ($assignment) {
                return [
                    'position' => $assignment->position?->name ?? 'Executive',
                    'scope_type' => $assignment->scope_type,
                ];
            })->values(),
        ]);
    }
}
