<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminContactDirectoryController extends Controller
{
    public function index(Request $request)
    {
        $actor = $request->user();
        $query = User::query()
            ->with(['alumniProfile.graduationSet', 'branchAffiliations.branch'])
            ->whereHas('alumniProfile');

        if (! $actor->isAdmin()) {
            $assignments = $actor->leadershipAssignments()
                ->where('is_active', true)
                ->where(fn ($q) => $q->whereNull('starts_at')->orWhereDate('starts_at', '<=', today()))
                ->where(fn ($q) => $q->whereNull('ends_at')->orWhereDate('ends_at', '>=', today()))
                ->get(['scope_type', 'scope_id']);

            $setIds = $assignments->where('scope_type', 'set')->pluck('scope_id')->filter()->unique();
            $branchIds = $assignments->where('scope_type', 'branch')->pluck('scope_id')->filter()->unique();

            abort_if($setIds->isEmpty() && $branchIds->isEmpty(), 403, 'No contact directory scope assigned.');

            $query->where(function ($scope) use ($setIds, $branchIds) {
                if ($setIds->isNotEmpty()) {
                    $scope->orWhereHas('alumniProfile', fn ($p) => $p->whereIn('graduation_set_id', $setIds));
                }
                if ($branchIds->isNotEmpty()) {
                    $scope->orWhereHas('branchAffiliations', fn ($b) => $b->whereIn('branch_id', $branchIds)->where('status', 'approved'));
                }
            });
        }

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhereHas('alumniProfile', fn ($p) => $p
                        ->where('phone', 'like', "%{$search}%")
                        ->orWhere('whatsapp_phone', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%"));
            });
        }

        $set = $request->query('set');
        if ($set) {
            $query->whereHas('alumniProfile', fn ($p) => $p->where('graduation_set_id', $set));
        }

        $branch = $request->query('branch');
        if ($branch) {
            $query->whereHas('branchAffiliations', fn ($b) => $b->where('branch_id', $branch)->where('status', 'approved'));
        }

        $members = $query->orderBy('name')->paginate(30)->withQueryString()->through(function (User $user) {
            $profile = $user->alumniProfile;
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'member_status' => $user->member_status,
                'phone' => $profile?->phone,
                'whatsapp_phone' => $profile?->whatsapp_phone ?: $profile?->phone,
                'sms_consent' => (bool) $profile?->sms_consent,
                'whatsapp_consent' => (bool) $profile?->whatsapp_consent,
                'preferred_contact' => $profile?->preferred_contact,
                'city' => $profile?->city,
                'country' => $profile?->country,
                'graduation_set' => $profile?->graduationSet?->name,
                'branches' => $user->branchAffiliations->where('status', 'approved')->pluck('branch.name')->filter()->values(),
            ];
        });

        return Inertia::render('Admin/Contacts/Index', [
            'members' => $members,
            'filters' => ['search' => $search, 'set' => $set, 'branch' => $branch],
            'canExport' => $actor->isAdmin(),
        ]);
    }
}
