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
            ->where('directory_visible', true)
            ->whereHas('user', fn ($u) => $u->where('member_status', 'approved'));

        if (! empty($filters['search'])) {
            $term = trim($filters['search']);
            $query->where(function ($q) use ($term) {
                $q->where('public_name', true)->where(function ($names) use ($term) {
                    $names->where('first_name', 'like', "%{$term}%")
                    ->orWhere('last_name', 'like', "%{$term}%")
                    ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$term}%"]);
                });
            });
        }

        if (! empty($filters['graduation_set_id'])) {
            $query->where('public_graduation_set', true)->where('graduation_set_id', $filters['graduation_set_id']);
        }
        if (! empty($filters['country'])) {
            $query->where('public_location', true)->where('country', $filters['country']);
        }
        if (! empty($filters['city'])) {
            $query->where('public_location', true)->where('city', 'like', '%' . trim($filters['city']) . '%');
        }
        if (! empty($filters['profession'])) {
            $query->where('public_profession', true)->where('profession', 'like', '%' . trim($filters['profession']) . '%');
        }
        if (! empty($filters['branch_id'])) {
            // Branch discovery is location-based; this does not imply formal branch membership.
            $query->whereExists(function ($sub) use ($filters) {
                $sub->selectRaw('1')
                    ->from('branch_cities')
                    ->where('alumni_profiles.public_location', true)
                    ->whereColumn('branch_cities.city_normalized', 'alumni_profiles.city_normalized')
                    ->whereColumn('branch_cities.country_normalized', 'alumni_profiles.country_normalized')
                    ->where('branch_cities.branch_id', $filters['branch_id']);
            });
        }

        $alumni = $query->with('user:id,email')->orderBy('last_name')->orderBy('first_name')->paginate(15)->withQueryString();
        $alumni->getCollection()->transform(function (AlumniProfile $profile) {
            $photo = $profile->public_photo ? $profile->photo : null;
            if ($photo && ! str_starts_with($photo, '/')) {
                $photo = Storage::disk('public')->url($photo);
            }

            return [
                'id' => $profile->id,
                'first_name' => $profile->public_name ? $profile->first_name : 'BBHS',
                'last_name' => $profile->public_name ? $profile->last_name : 'Alumni',
                'class_set' => $profile->public_graduation_set ? ($profile->graduationSet?->name ?? $profile->class_set) : null,
                'graduation_set' => $profile->public_graduation_set && $profile->graduationSet ? [
                    'name' => $profile->graduationSet->name,
                    'graduation_year' => $profile->graduationSet->graduation_year,
                ] : null,
                'profession' => $profile->public_profession ? $profile->profession : null,
                'city' => $profile->public_location ? $profile->city : null,
                'country' => $profile->public_location ? $profile->country : null,
                'photo' => $photo,
                'bio' => $profile->public_bio ? $profile->bio : null,
                'phone' => $profile->phone ? $this->maskPhone($profile->phone) : null,
                'email' => $profile->user?->email ? $this->maskEmail($profile->user->email) : null,
            ];
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
            'countries' => AlumniProfile::query()->where('approved', true)->where('directory_visible', true)->where('public_location', true)->whereHas('user', fn ($u) => $u->where('member_status', 'approved'))
                ->whereNotNull('country')->where('country', '<>', '')
                ->distinct()->orderBy('country')->pluck('country'),
        ]);
    }

    public function show(Request $request, AlumniProfile $alumniProfile)
    {
        abort_unless($alumniProfile->approved && $alumniProfile->directory_visible, 404);

        $alumniProfile->load(['graduationSet:id,name,graduation_year', 'user:id,email']);

        return Inertia::render('AlumniDirectory/Show', [
            'person' => [
                'id' => $alumniProfile->id,
                'first_name' => $alumniProfile->public_name ? $alumniProfile->first_name : 'BBHS',
                'last_name' => $alumniProfile->public_name ? $alumniProfile->last_name : 'Alumni',
                'class_set' => $alumniProfile->public_graduation_set ? ($alumniProfile->graduationSet?->name ?? $alumniProfile->class_set) : null,
                'graduation_year' => $alumniProfile->public_graduation_set ? $alumniProfile->graduationSet?->graduation_year : null,
                'profession' => $alumniProfile->public_profession ? $alumniProfile->profession : null,
                'city' => $alumniProfile->public_location ? $alumniProfile->city : null,
                'country' => $alumniProfile->public_location ? $alumniProfile->country : null,
                'photo' => $alumniProfile->public_photo && $alumniProfile->photo && ! str_starts_with($alumniProfile->photo, '/') ? Storage::disk('public')->url($alumniProfile->photo) : ($alumniProfile->public_photo ? $alumniProfile->photo : null),
                'bio' => $alumniProfile->public_bio ? $alumniProfile->bio : null,
                'phone' => $alumniProfile->phone ? $this->maskPhone($alumniProfile->phone) : null,
                'email' => $alumniProfile->user?->email ? $this->maskEmail($alumniProfile->user->email) : null,
            ],
        ]);
    }

    private function maskPhone(string $phone): string
    {
        $digits = preg_replace('/\\D+/', '', $phone);
        if (strlen($digits) < 5) return '••••';
        return substr($digits, 0, 3) . str_repeat('*', max(3, strlen($digits) - 5)) . substr($digits, -2);
    }

    private function maskEmail(string $email): string
    {
        [$local, $domain] = array_pad(explode('@', $email, 2), 2, '');
        if ($domain === '') return '••••';
        return substr($local, 0, 2) . str_repeat('*', max(2, strlen($local) - 2)) . '@' . $domain;
    }

}
