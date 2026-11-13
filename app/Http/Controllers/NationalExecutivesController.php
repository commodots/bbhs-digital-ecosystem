<?php

namespace App\Http\Controllers;

use App\Models\LeadershipAssignment;
use Inertia\Inertia;

class NationalExecutivesController extends Controller
{
    public function index()
    {
        $executives = LeadershipAssignment::query()
            ->with(['user.alumniProfile.graduationSet', 'position'])
            ->where('scope_type', 'global')
            ->whereNull('scope_id')
            ->where('is_active', true)
            ->where(function ($q) { $q->whereNull('ends_at')->orWhereDate('ends_at', '>=', now()->toDateString()); })
            ->whereHas('position', fn ($q) => $q->where('is_active', true))
            ->get()
            ->sortBy(fn ($a) => $a->position->sort_order)
            ->map(fn ($a) => [
                'id' => $a->id,
                'name' => $a->user->name,
                'position' => $a->position->name,
                'set' => $a->user->alumniProfile?->graduationSet?->graduation_year,
                'photo' => $a->user->alumniProfile?->photo,
                'profile_url' => ($a->user->alumniProfile?->approved && $a->user->alumniProfile?->directory_visible)
                    ? route('alumni.directory.show', $a->user->alumniProfile->id)
                    : null,
            ])->values();

        return Inertia::render('Executives', [
            'executives' => $executives,
            'termLabel' => 'Serving until January 2027',
        ]);
    }
}
