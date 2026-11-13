<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\AlumniSpotlight;
use Illuminate\Support\Facades\Storage;
use App\Models\Branch;
use App\Models\ExecutivePosition;
use App\Models\GraduationSet;
use App\Models\LeadershipAssignment;
use Inertia\Inertia;

class AlumniLandingController extends Controller
{
    public function index()
    {
        $approvedMembers = AlumniProfile::query()->where('approved', true)->count();
        $representedSets = AlumniProfile::query()->where('approved', true)
            ->whereNotNull('graduation_set_id')->distinct('graduation_set_id')->count('graduation_set_id');

        return Inertia::render('Alumni', [
            'stats' => [
                'members' => $approvedMembers,
                'sets' => $representedSets,
                'branches' => Branch::query()->where('is_active', true)->count(),
            ],
            'graduationSets' => GraduationSet::query()->where('is_active', true)
                ->withCount(['alumniProfiles as approved_members_count' => fn ($q) => $q->where('approved', true)])
                ->orderByDesc('graduation_year')->get(['id', 'name', 'graduation_year']),
            'spotlights' => AlumniSpotlight::with('alumniProfile.graduationSet')->where('is_active', true)
                ->whereHas('alumniProfile', fn ($q) => $q->where('approved', true)->whereHas('user', fn ($u) => $u->where('member_status', 'approved')))
                ->orderBy('sort_order')->orderBy('id')->get()->map(fn ($s) => [
                    'id' => $s->id, 'profile_id' => $s->alumniProfile->id,
                    'name' => trim($s->alumniProfile->first_name . ' ' . $s->alumniProfile->last_name),
                    'class_set' => $s->alumniProfile->graduationSet?->name ?? $s->alumniProfile->class_set,
                    'photo' => $s->alumniProfile->photo ? (str_starts_with($s->alumniProfile->photo, '/') || str_starts_with($s->alumniProfile->photo, 'http') ? $s->alumniProfile->photo : Storage::disk('public')->url($s->alumniProfile->photo)) : null,
                    'headline' => $s->headline, 'writeup' => $s->writeup,
                ])->values(),
            'executiveCount' => LeadershipAssignment::query()->where('scope_type', 'global')
                ->where('is_active', true)->whereHas('position', fn ($q) => $q->where('is_active', true))->count(),
        ]);
    }
}
