<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchCity;
use App\Models\ExecutivePosition;
use App\Models\GraduationSet;
use App\Models\LeadershipAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class OrganisationController extends Controller
{
    /** Super Admin interface for BBHS organisational administration. */
    public function index()
    {
        $assignmentRelations = function ($query) {
            $query->where('is_active', true)
                ->with([
                    'user:id,name,email',
                    'position:id,name,scope_type,code',
                ])
                ->orderBy('executive_position_id');
        };

        $sets = GraduationSet::query()
            ->with(['leadershipAssignments' => $assignmentRelations])
            ->orderBy('graduation_year')
            ->get(['id', 'graduation_year', 'name', 'is_active']);

        $branches = Branch::query()
            ->with(['cities', 'leadershipAssignments' => $assignmentRelations])
            ->orderBy('name')
            ->get(['id', 'name', 'slug', 'description', 'is_active']);

        $assignments = LeadershipAssignment::query()
            ->with([
                'user:id,name,email',
                'position:id,name,scope_type,code',
            ])
            ->where('is_active', true)
            ->orderBy('scope_type')
            ->orderBy('scope_id')
            ->orderBy('executive_position_id')
            ->get()
            ->map(function (LeadershipAssignment $assignment) use ($sets, $branches) {
                $assignment->scope_name = match ($assignment->scope_type) {
                    'global' => 'Global BBHS',
                    'set' => optional($sets->firstWhere('id', $assignment->scope_id))->name ?? 'Unknown set',
                    'branch' => optional($branches->firstWhere('id', $assignment->scope_id))->name ?? 'Unknown branch',
                    default => 'Unknown scope',
                };

                return $assignment;
            });

        return Inertia::render('Admin/Organisation/Index', [
            'sets' => $sets,
            'branches' => $branches,
            'positions' => ExecutivePosition::query()
                ->where('is_active', true)
                ->orderBy('scope_type')
                ->orderBy('sort_order')
                ->get(['id', 'scope_type', 'name', 'code', 'sort_order']),
            'members' => User::query()
                ->where('is_admin', false)
                ->with('alumniProfile:id,user_id,graduation_set_id,first_name,last_name,photo')
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'member_status']),
            'assignments' => $assignments,
            'stats' => [
                'sets' => $sets->count(),
                'branches' => $branches->count(),
                'global_executives' => $assignments->where('scope_type', 'global')->count(),
                'set_executives' => $assignments->where('scope_type', 'set')->count(),
                'branch_executives' => $assignments->where('scope_type', 'branch')->count(),
            ],
        ]);
    }

    /** Add a configurable executive title at one organisational level. */
    public function storePosition(Request $request)
    {
        $validated = $request->validate([
            'scope_type' => ['required', Rule::in(['global', 'set', 'branch'])],
            'name' => ['required', 'string', 'max:100'],
        ]);

        $baseCode = Str::slug($validated['name']) ?: 'executive-position';
        $code = $baseCode;
        $suffix = 2;

        while (ExecutivePosition::query()
            ->where('scope_type', $validated['scope_type'])
            ->where('code', $code)
            ->exists()) {
            $code = $baseCode . '-' . $suffix++;
        }

        $sortOrder = ((int) ExecutivePosition::query()
            ->where('scope_type', $validated['scope_type'])
            ->max('sort_order')) + 10;

        ExecutivePosition::create([
            'scope_type' => $validated['scope_type'],
            'name' => $validated['name'],
            'code' => $code,
            'sort_order' => $sortOrder,
            'is_active' => true,
        ]);

        return back()->with('success', 'Executive position added successfully.');
    }

    /** Assign a member to a global, set or branch position. Super Admin can bootstrap pending set/branch leadership. */
    public function storeAssignment(Request $request)
    {
        $validated = $request->validate([
            'scope_type' => ['required', Rule::in(['global', 'set', 'branch'])],
            'scope_id' => ['nullable', 'integer', 'required_if:scope_type,set,branch'],
            'executive_position_id' => ['required', 'integer', 'exists:executive_positions,id'],
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'starts_at' => ['nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $position = ExecutivePosition::query()
            ->whereKey($validated['executive_position_id'])
            ->where('is_active', true)
            ->firstOrFail();

        if ($position->scope_type !== $validated['scope_type']) {
            throw ValidationException::withMessages([
                'executive_position_id' => 'The selected position does not belong to this organisational level.',
            ]);
        }

        $scopeId = $validated['scope_type'] === 'global'
            ? null
            : (int) $validated['scope_id'];

        if ($validated['scope_type'] === 'set') {
            GraduationSet::query()->whereKey($scopeId)->where('is_active', true)->firstOrFail();
        } elseif ($validated['scope_type'] === 'branch') {
            Branch::query()->whereKey($scopeId)->where('is_active', true)->firstOrFail();
        }

        $member = User::query()
            ->with('alumniProfile')
            ->whereKey($validated['user_id'])
            ->where('is_admin', false)
            ->first();

        if (! $member) {
            throw ValidationException::withMessages(['user_id' => 'Select a registered BBHS member.']);
        }

        if ($validated['scope_type'] === 'set') {
            if ((int) ($member->alumniProfile?->graduation_set_id) !== $scopeId) {
                throw ValidationException::withMessages(['user_id' => 'The selected member is not registered in this graduation set.']);
            }
        }

        $slotQuery = LeadershipAssignment::query()
            ->where('scope_type', $validated['scope_type'])
            ->where('executive_position_id', $position->id)
            ->where('is_active', true);

        $scopeId === null
            ? $slotQuery->whereNull('scope_id')
            : $slotQuery->where('scope_id', $scopeId);

        if ($slotQuery->exists()) {
            throw ValidationException::withMessages([
                'executive_position_id' => 'This executive position already has an active appointee. End the existing appointment before assigning a replacement.',
            ]);
        }

        DB::transaction(function () use ($validated, $scopeId, $request, $member) {
            LeadershipAssignment::create([
                'user_id' => $validated['user_id'],
                'executive_position_id' => $validated['executive_position_id'],
                'scope_type' => $validated['scope_type'],
                'scope_id' => $scopeId,
                'starts_at' => $validated['starts_at'] ?? now()->toDateString(),
                'ends_at' => $validated['ends_at'] ?? null,
                'is_active' => true,
                'appointed_by' => $request->user()->id,
                'notes' => $validated['notes'] ?? null,
            ]);

            // Appointment by Super Admin resolves the bootstrap deadlock for the appointed scope.
            if ($validated['scope_type'] === 'set') {
                $member->update(['member_status' => 'approved']);
                $member->alumniProfile?->update(['approved' => true]);
                MembershipVerification::query()
                    ->where('user_id', $member->id)
                    ->where('verification_type', 'set')
                    ->where('scope_type', 'set')
                    ->where('scope_id', $scopeId)
                    ->update([
                        'status' => 'approved',
                        'reviewed_by' => $request->user()->id,
                        'reviewed_at' => now(),
                        'review_notes' => 'Set membership approved through Super Admin executive appointment.',
                    ]);
                MembershipAuditLog::create([
                    'user_id' => $member->id,
                    'actor_id' => $request->user()->id,
                    'action' => 'set_membership_approved_by_executive_appointment',
                    'scope_type' => 'set',
                    'scope_id' => $scopeId,
                    'details' => ['executive_position_id' => $validated['executive_position_id']],
                    'ip_address' => $request->ip(),
                ]);
            } elseif ($validated['scope_type'] === 'branch') {
                MemberBranch::query()
                    ->where('user_id', $member->id)
                    ->where('branch_id', $scopeId)
                    ->update([
                        'status' => 'approved',
                        'reviewed_by' => $request->user()->id,
                        'reviewed_at' => now(),
                        'review_notes' => 'Branch affiliation approved through Super Admin executive appointment.',
                    ]);
                MembershipVerification::query()
                    ->where('user_id', $member->id)
                    ->where('verification_type', 'branch')
                    ->where('scope_type', 'branch')
                    ->where('scope_id', $scopeId)
                    ->update([
                        'status' => 'approved',
                        'reviewed_by' => $request->user()->id,
                        'reviewed_at' => now(),
                        'review_notes' => 'Branch affiliation approved through Super Admin executive appointment.',
                    ]);
                MembershipAuditLog::create([
                    'user_id' => $member->id,
                    'actor_id' => $request->user()->id,
                    'action' => 'branch_membership_approved_by_executive_appointment',
                    'scope_type' => 'branch',
                    'scope_id' => $scopeId,
                    'details' => ['executive_position_id' => $validated['executive_position_id']],
                    'ip_address' => $request->ip(),
                ]);
            }
        });

        return back()->with('success', 'Executive appointment created. The appointed member’s relevant set or branch affiliation has also been approved.');
    }

    /** End an executive appointment without deleting its history. */
    public function deactivateAssignment(LeadershipAssignment $assignment)
    {
        if (! $assignment->is_active) {
            return back()->with('success', 'This appointment is already inactive.');
        }

        $assignment->update([
            'is_active' => false,
            'ends_at' => $assignment->ends_at ?? now()->toDateString(),
        ]);

        return back()->with('success', 'Executive appointment ended. Its history has been retained.');
    }

    /** Create a new branch and optionally map its first city. */
    public function storeBranch(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150', 'unique:branches,name'],
            'description' => ['nullable', 'string', 'max:2000'],
            'city' => ['required', 'string', 'max:150'],
            'country' => ['required', 'string', 'max:150'],
        ]);

        DB::transaction(function () use ($validated) {
            $baseSlug = Str::slug($validated['name']) ?: 'branch';
            $slug = $baseSlug;
            $suffix = 2;
            while (Branch::where('slug', $slug)->exists()) {
                $slug = $baseSlug . '-' . $suffix++;
            }

            $branch = Branch::create([
                'name' => $validated['name'],
                'slug' => $slug,
                'description' => $validated['description'] ?? null,
                'is_active' => true,
            ]);

            $city = trim($validated['city']);
            $country = trim($validated['country']);

            BranchCity::create([
                'branch_id' => $branch->id,
                'city' => $city,
                'country' => $country,
                'city_normalized' => Str::lower($city),
                'country_normalized' => Str::lower($country),
            ]);
        });

        return back()->with('success', 'Branch and city mapping created successfully.');
    }

    /** Add another city served by an existing branch. */
    public function addCity(Request $request, Branch $branch)
    {
        $validated = $request->validate([
            'city' => ['required', 'string', 'max:150'],
            'country' => ['required', 'string', 'max:150'],
        ]);

        $city = trim($validated['city']);
        $country = trim($validated['country']);
        $cityNormalized = Str::lower($city);
        $countryNormalized = Str::lower($country);

        $exists = BranchCity::query()
            ->where('branch_id', $branch->id)
            ->where('city_normalized', $cityNormalized)
            ->where('country_normalized', $countryNormalized)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages(['city' => 'This city is already mapped to the selected branch.']);
        }

        BranchCity::create([
            'branch_id' => $branch->id,
            'city' => $city,
            'country' => $country,
            'city_normalized' => $cityNormalized,
            'country_normalized' => $countryNormalized,
        ]);

        return back()->with('success', 'Branch city added successfully.');
    }

    /** Remove a city mapping; the branch itself and its membership history remain. */
    public function removeCity(Branch $branch, BranchCity $city)
    {
        abort_unless($city->branch_id === $branch->id, 404);
        $city->delete();

        return back()->with('success', 'City mapping removed.');
    }
}
