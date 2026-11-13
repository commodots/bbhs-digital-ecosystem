<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use App\Models\LeadershipAssignment;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function share(Request $request): array
    {
        $user = $request->user();

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user ? [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'is_admin' => (bool) $user->isAdmin(),
                    'is_executive' => $user->leadershipAssignments()
                        ->where('is_active', true)
                        ->where(fn ($q) => $q->whereNull('starts_at')->orWhereDate('starts_at', '<=', now()->toDateString()))
                        ->where(fn ($q) => $q->whereNull('ends_at')->orWhereDate('ends_at', '>=', now()->toDateString()))
                        ->exists(),
                ] : null,
            ],
            'notifications' => [
                'unread_count' => $user ? $user->unreadNotifications()->count() : 0,
                'recent' => $user ? $user->notifications()->latest()->limit(5)->get()->map(fn ($item) => [
                    'id' => $item->id,
                    'title' => data_get($item->data, 'title', 'BBHS Notification'),
                    'message' => data_get($item->data, 'message', ''),
                    'category' => data_get($item->data, 'category', 'general'),
                    'read_at' => $item->read_at,
                    'created_at' => $item->created_at,
                ])->values() : [],
            ],
            'sponsorProfiles' => fn () => LeadershipAssignment::query()
                ->with(['user.alumniProfile', 'position'])
                ->where('scope_type', 'global')
                ->whereNull('scope_id')
                ->where('is_active', true)
                ->where(function ($q) { $q->whereNull('ends_at')->orWhereDate('ends_at', '>=', now()->toDateString()); })
                ->whereHas('position', fn ($q) => $q->where('is_active', true)->where('name', 'like', '%Vice President%'))
                ->get()
                ->sortBy(fn ($a) => $a->position->sort_order)
                ->map(fn ($a) => [
                    'name' => $a->user->name,
                    'position' => $a->position->name,
                    'profile_url' => ($a->user->alumniProfile?->approved && $a->user->alumniProfile?->directory_visible)
                        ? route('alumni.directory.show', $a->user->alumniProfile->id)
                        : null,
                ])->values(),
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
        ]);
    }
}
