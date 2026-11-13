<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\BranchCity;
use App\Notifications\BbhsPlatformNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        return Inertia::render('Events', [
            'events' => $this->visibleEventsQuery()
                ->where('published', true)
                ->orderBy('starts_at')
                ->paginate(12)
                ->through(fn ($event) => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'slug' => $event->slug,
                    'excerpt' => $event->excerpt,
                    'image' => $event->image,
                    'starts_at' => optional($event->starts_at)->format('Y-m-d H:i:s'),
                    'date' => optional($event->starts_at)->format('d'),
                    'month' => optional($event->starts_at)->format('M'),
                    'time' => optional($event->starts_at)->format('g:i A'),
                    'venue' => $event->venue,
                    'location' => $event->location,
                    'capacity' => $event->capacity,
                    'registration_open' => $event->registration_open,
                ]),
        ]);
    }

    public function show(Event $event)
    {
        abort_unless($event->published && $this->canView($event), 404);

        return Inertia::render('EventDetail', [
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'excerpt' => $event->excerpt,
                'description' => $event->description,
                'image' => $event->image,
                'starts_at' => optional($event->starts_at)->format('M d, Y g:i A'),
                'ends_at' => optional($event->ends_at)->format('M d, Y g:i A'),
                'venue' => $event->venue,
                'location' => $event->location,
                'capacity' => $event->capacity,
                'registration_open' => $event->registration_open,
                'requires_registration' => $event->requires_registration,
                'registration_count' => $event->registrations()->where('status', 'registered')->count(),
                'remaining_capacity' => $event->capacity === null ? null : max(0, $event->capacity - $event->registrations()->where('status', 'registered')->count()),
                'is_authenticated' => (bool) Auth::check(),
                'member_approved' => Auth::check() && Auth::user()->member_status === 'approved',
                'registration' => Auth::check()
                    ? $event->registrations()->where('user_id', Auth::id())->first(['id', 'status'])
                    : null,
            ],
        ]);
    }

    public function register(Request $request, Event $event)
    {
        abort_unless($event->published && $this->canView($event), 404);
        abort_unless($request->user()->member_status === 'approved', 403, 'Only approved BBHS members can register for events.');
        if (! $event->requires_registration) {
            throw ValidationException::withMessages(['registration' => 'Registration is not required for this event.']);
        }
        if (! $event->registration_open) {
            throw ValidationException::withMessages(['registration' => 'Registration for this event is closed.']);
        }

        $registration = DB::transaction(function () use ($event, $request) {
            $lockedEvent = Event::whereKey($event->id)->lockForUpdate()->firstOrFail();
            $existing = EventRegistration::where('event_id', $lockedEvent->id)
                ->where('user_id', $request->user()->id)->first();

            if ($existing && $existing->status === 'registered') {
                return $existing;
            }

            $count = EventRegistration::where('event_id', $lockedEvent->id)
                ->where('status', 'registered')->count();

            if ($lockedEvent->capacity !== null && $count >= $lockedEvent->capacity) {
                throw ValidationException::withMessages(['registration' => 'This event has reached its registration capacity.']);
            }

            if ($existing) {
                $existing->update(['status' => 'registered']);
                return $existing->fresh();
            }

            return EventRegistration::create([
                'event_id' => $lockedEvent->id,
                'user_id' => $request->user()->id,
                'status' => 'registered',
            ]);
        });

        $request->user()->notify(new BbhsPlatformNotification(
            'Event registration confirmed',
            'Your place has been registered for ' . $event->title . '.',
            'events',
            '/events/' . $event->slug,
            ['event_id' => $event->id, 'registration_id' => $registration->id]
        ));

        return back()->with('success', 'Your event registration is confirmed.');
    }

    public function cancelRegistration(Request $request, Event $event)
    {
        abort_unless($event->published && $this->canView($event), 404);

        $registration = $event->registrations()
            ->where('user_id', $request->user()->id)
            ->where('status', 'registered')
            ->firstOrFail();

        $registration->update(['status' => 'cancelled']);

        $request->user()->notify(new BbhsPlatformNotification(
            'Event registration cancelled',
            'Your registration for ' . $event->title . ' has been cancelled.',
            'events',
            '/events/' . $event->slug,
            ['event_id' => $event->id, 'registration_id' => $registration->id]
        ));

        return back()->with('success', 'Your registration has been cancelled.');
    }

    private function visibleEventsQuery()
    {
        $user = Auth::user();
        if ($user && $user->isAdmin()) return Event::query();

        $profile = $user?->alumniProfile;
        $setId = $profile?->graduation_set_id;
        $approvedBranchIds = $user
            ? $user->branchAffiliations()->where('status', 'approved')->pluck('branch_id')->all()
            : [];

        $residentBranchIds = ($profile && $profile->city_normalized && $profile->country_normalized)
            ? BranchCity::query()
                ->where('city_normalized', $profile->city_normalized)
                ->where('country_normalized', $profile->country_normalized)
                ->pluck('branch_id')->all()
            : [];

        return Event::query()->where(function ($query) use ($setId, $approvedBranchIds, $residentBranchIds) {
            $query->where('scope_type', 'global');

            if ($setId) {
                $query->orWhere(fn ($q) => $q->where('scope_type', 'set')->where('graduation_set_id', $setId));
            }

            if ($approvedBranchIds) {
                $query->orWhere(fn ($q) => $q->where('scope_type', 'branch')->whereIn('branch_id', $approvedBranchIds));
            }

            if ($residentBranchIds) {
                $query->orWhere(fn ($q) => $q->where('scope_type', 'branch')
                    ->where('allow_non_branch_residents', true)->whereIn('branch_id', $residentBranchIds));
            }

            if ($setId || $approvedBranchIds || $residentBranchIds) {
                $query->orWhere(function ($joint) use ($setId, $approvedBranchIds, $residentBranchIds) {
                    $joint->where('scope_type', 'joint')->where(function ($visibility) use ($setId, $approvedBranchIds, $residentBranchIds) {
                    if ($setId) {
                        $visibility->whereHas('scopes', fn ($q) => $q->where('scope_type', 'set')->where('scope_id', $setId));
                    }
                    if ($approvedBranchIds) {
                        $method = $setId ? 'orWhereHas' : 'whereHas';
                        $visibility->{$method}('scopes', fn ($q) => $q->where('scope_type', 'branch')->whereIn('scope_id', $approvedBranchIds));
                    }
                    if ($residentBranchIds) {
                        $method = ($setId || $approvedBranchIds) ? 'orWhere' : 'where';
                        $visibility->{$method}(fn ($q) => $q->where('allow_non_branch_residents', true)
                            ->whereHas('scopes', fn ($scope) => $scope->where('scope_type', 'branch')->whereIn('scope_id', $residentBranchIds)));
                    }
                    });
                });
            }
        });
    }

    private function canView(Event $event): bool
    {
        $user = Auth::user();
        if ($event->scope_type === 'global' || ($user && $user->isAdmin())) return true;
        if (!$user) return false;

        $profile = $user->alumniProfile;
        $setId = $profile?->graduation_set_id;
        $branchIds = $user->branchAffiliations()->where('status', 'approved')->pluck('branch_id')->all();

        if ($profile && $profile->city_normalized && $profile->country_normalized) {
            $residentIds = BranchCity::where('city_normalized', $profile->city_normalized)
                ->where('country_normalized', $profile->country_normalized)->pluck('branch_id')->all();
        } else {
            $residentIds = [];
        }
        $residentIds = array_values(array_unique($residentIds));

        if ($event->scope_type === 'set') return $setId && (int) $event->graduation_set_id === (int) $setId;
        if ($event->scope_type === 'branch') {
            return in_array((int) $event->branch_id, $branchIds, true)
                || ($event->allow_non_branch_residents && in_array((int) $event->branch_id, $residentIds, true));
        }
        if ($event->scope_type === 'joint') {
            $allowedBranches = array_values(array_unique(array_merge($branchIds, $event->allow_non_branch_residents ? $residentIds : [])));
            if (!$setId && !$allowedBranches) return false;

            return $event->scopes()->where(function ($q) use ($setId, $allowedBranches) {
                if ($setId) $q->where(fn ($x) => $x->where('scope_type', 'set')->where('scope_id', $setId));
                if ($allowedBranches) {
                    $method = $setId ? 'orWhere' : 'where';
                    $q->{$method}(fn ($x) => $x->where('scope_type', 'branch')->whereIn('scope_id', $allowedBranches));
                }
            })->exists();
        }
        return false;
    }
}
