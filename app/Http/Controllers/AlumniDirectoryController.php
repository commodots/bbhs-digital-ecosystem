<?php

namespace App\Http\Controllers;

use App\Models\AlumniProfile;
use App\Models\Branch;
use App\Models\GraduationSet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class AlumniDirectoryController extends Controller
{
    public function index(Request $request)
    {
        abort_unless($request->user()->isAdmin() || ($request->user()->member_status === 'approved' && $request->user()->alumniProfile?->approved), 403);

        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:120'],
            'graduation_set_id' => ['nullable', 'integer'],
            'country' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:150'],
            'branch_id' => ['nullable', 'integer'],
            'profession' => ['nullable', 'string', 'max:150'],
        ]);

        $query = AlumniProfile::query()
            ->with('graduationSet:id,name,graduation_year')
            ->where('approved', true)
            ->whereHas('user', fn ($u) => $u->where('member_status', 'approved'));

        if (! empty($filters['search'])) {
            $term = trim($filters['search']);
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"]);
            });
        }

        if (! empty($filters['graduation_set_id'])) {
            $query->where('graduation_set_id', $filters['graduation_set_id']);
        }
        if (! empty($filters['country'])) {
            $query->where('country', $filters['country']);
        }
        if (! empty($filters['city'])) {
            $query->where('city', 'like', '%' . trim($filters['city']) . '%');
        }
        if (! empty($filters['profession'])) {
            $query->where('profession', 'like', '%' . trim($filters['profession']) . '%');
        }
        if (! empty($filters['branch_id'])) {
            // Branch discovery is location-based; this does not imply formal branch membership.
            $query->whereExists(function ($sub) use ($filters) {
                $sub->selectRaw('1')
                    ->from('branch_cities')
                    ->whereColumn('branch_cities.city_normalized', 'alumni_profiles.city_normalized')
                    ->whereColumn('branch_cities.country_normalized', 'alumni_profiles.country_normalized')
                    ->where('branch_cities.branch_id', $filters['branch_id']);
            });
        }

        $alumni = $query->with('user:id,email')->orderBy('last_name')->orderBy('first_name')->paginate(15)->withQueryString();
        $alumni->getCollection()->transform(function (AlumniProfile $profile) {
            if ($profile->photo && ! str_starts_with($profile->photo, '/')) {
                $profile->photo = Storage::disk('public')->url($profile->photo);
            }
            // Contact details are deliberately masked in the shared directory.
            $profile->phone = $profile->phone ? '***' : null;
            $profile->email = $profile->user?->email ? '***' : null;
            unset($profile->user);
            return $profile;
        });

        return Inertia::render('AlumniDirectory', [
            'alumni' => $alumni,
            'filters' => array_merge([
                'search' => '', 'graduation_set_id' => '', 'country' => '',
                'city' => '', 'branch_id' => '', 'profession' => '',
            ], $filters),
            'graduationSets' => GraduationSet::where('is_active', true)
                ->orderBy('graduation_year', 'desc')->get(['id', 'name', 'graduation_year']),
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'countries' => AlumniProfile::query()->where('approved', true)->whereHas('user', fn ($u) => $u->where('member_status', 'approved'))
                ->whereNotNull('country')->where('country', '<>', '')
                ->distinct()->orderBy('country')->pluck('country'),
        ]);
    }

    public function show(Request $request, AlumniProfile $alumniProfile)
    {
        abort_unless($request->user()->isAdmin() || ($request->user()->member_status === 'approved' && $request->user()->alumniProfile?->approved), 403);
        abort_unless($alumniProfile->approved && $alumniProfile->directory_visible, 404);

        $alumniProfile->load(['graduationSet:id,name,graduation_year', 'user:id,email']);

        return Inertia::render('AlumniDirectory/Show', [
            'person' => [
                'id' => $alumniProfile->id,
                'first_name' => $alumniProfile->first_name,
                'last_name' => $alumniProfile->last_name,
                'class_set' => $alumniProfile->graduationSet?->name ?? $alumniProfile->class_set,
                'graduation_year' => $alumniProfile->graduationSet?->graduation_year,
                'profession' => $alumniProfile->profession,
                'city' => $alumniProfile->city,
                'country' => $alumniProfile->country,
                'photo' => $alumniProfile->photo && ! str_starts_with($alumniProfile->photo, '/')
                    ? Storage::disk('public')->url($alumniProfile->photo)
                    : $alumniProfile->photo,
                'bio' => $alumniProfile->bio,
                'phone' => $alumniProfile->phone ? '***' : null,
                'email' => $alumniProfile->user?->email ? '***' : null,
            ],
        ]);
    }
}
