<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GraduationSet;
use App\Models\LeadershipAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminMembershipReportController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $scope = $this->resolveScope($user, $request);
        $base = $this->memberQuery($scope);

        $total = (clone $base)->count();
        $statuses = (clone $base)->select('member_status', DB::raw('COUNT(*) as aggregate'))
            ->groupBy('member_status')->pluck('aggregate', 'member_status');

        $setRows = DB::table('users')
            ->join('alumni_profiles', 'alumni_profiles.user_id', '=', 'users.id')
            ->leftJoin('graduation_sets', 'graduation_sets.id', '=', 'alumni_profiles.graduation_set_id')
            ->where('users.is_admin', false);
        if ($scope['type'] === 'set') {
            $setRows->where('alumni_profiles.graduation_set_id', $scope['id']);
        } elseif ($scope['type'] === 'branch') {
            $setRows->join('member_branches as mb_set', 'mb_set.user_id', '=', 'users.id')
                ->where('mb_set.branch_id', $scope['id'])->whereIn('mb_set.status', ['pending', 'approved']);
        }
        $bySet = $setRows->selectRaw("COALESCE(graduation_sets.name, alumni_profiles.class_set, 'Not assigned') as label, COUNT(DISTINCT users.id) as total")
            ->groupBy('label')->orderByDesc('total')->get();

        $branchRows = DB::table('member_branches')
            ->join('branches', 'branches.id', '=', 'member_branches.branch_id')
            ->join('users', 'users.id', '=', 'member_branches.user_id')
            ->where('users.is_admin', false)->whereIn('member_branches.status', ['pending', 'approved']);
        if ($scope['type'] === 'branch') {
            $branchRows->where('member_branches.branch_id', $scope['id']);
        } elseif ($scope['type'] === 'set') {
            $branchRows->join('alumni_profiles as ap_branch', 'ap_branch.user_id', '=', 'users.id')
                ->where('ap_branch.graduation_set_id', $scope['id']);
        }
        $byBranch = $branchRows->select('branches.name as label')
            ->selectRaw('COUNT(DISTINCT users.id) as total')
            ->groupBy('branches.id', 'branches.name')->orderByDesc('total')->get();

        $monthlyQuery = (clone $base)->where('users.created_at', '>=', now()->subMonths(11)->startOfMonth());
        $monthly = $monthlyQuery->selectRaw("DATE_FORMAT(users.created_at, '%Y-%m') as month_key, COUNT(*) as total")
            ->groupBy('month_key')->orderBy('month_key')->get();
        $monthlyMap = $monthly->keyBy('month_key');
        $trend = [];
        $cursor = now()->subMonths(11)->startOfMonth();
        for ($i = 0; $i < 12; $i++) {
            $key = $cursor->format('Y-m');
            $trend[] = ['month' => $cursor->format('M Y'), 'total' => (int) ($monthlyMap[$key]->total ?? 0)];
            $cursor->addMonth();
        }

        $pendingVerifications = DB::table('membership_verifications')->where('status', 'pending');
        if ($scope['type'] === 'set' || $scope['type'] === 'branch') {
            $pendingVerifications->where('scope_type', $scope['type'])->where('scope_id', $scope['id']);
        }

        return Inertia::render('Admin/MembershipReports/Index', [
            'scope' => $scope,
            'summary' => [
                'total' => $total,
                'approved' => (int) ($statuses['approved'] ?? 0),
                'pending' => (int) ($statuses['pending'] ?? 0),
                'rejected' => (int) ($statuses['rejected'] ?? 0),
                'suspended' => (int) ($statuses['suspended'] ?? 0),
                'pending_verifications' => (int) $pendingVerifications->count(),
            ],
            'bySet' => $bySet,
            'byBranch' => $byBranch,
            'trend' => $trend,
            'filters' => ['scope_type' => $scope['type'], 'scope_id' => $scope['id']],
            'availableScopes' => $this->availableScopes($user),
        ]);
    }

    public function export(Request $request)
    {
        $scope = $this->resolveScope($request->user(), $request);
        $query = $this->memberQuery($scope)
            ->with('alumniProfile.graduationSet')
            ->orderBy('users.name');

        return response()->streamDownload(function () use ($query) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Name', 'Email', 'Phone', 'Graduation Set', 'Branch', 'Status', 'Registered At']);
            $query->chunk(500, function ($members) use ($out) {
                foreach ($members as $member) {
                    $branches = $member->branchAffiliations()
                        ->with('branch:id,name')->whereIn('status', ['pending', 'approved'])
                        ->get()->pluck('branch.name')->filter()->implode('; ');
                    fputcsv($out, [
                        $member->name,
                        $member->email,
                        $member->alumniProfile?->phone,
                        $member->alumniProfile?->graduationSet?->name ?? $member->alumniProfile?->class_set,
                        $branches,
                        $member->member_status,
                        $member->created_at?->toDateTimeString(),
                    ]);
                }
            });
            fclose($out);
        }, 'bbhs-membership-report-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    private function memberQuery(array $scope)
    {
        $query = User::query()->where('users.is_admin', false)->with('alumniProfile.graduationSet');
        if ($scope['type'] === 'set') {
            $query->whereHas('alumniProfile', fn ($q) => $q->where('graduation_set_id', $scope['id']));
        } elseif ($scope['type'] === 'branch') {
            $query->whereHas('branchAffiliations', fn ($q) => $q->where('branch_id', $scope['id'])->whereIn('status', ['pending', 'approved']));
        }
        return $query;
    }

    private function resolveScope($user, Request $request): array
    {
        if ($user->isAdmin()) {
            $type = $request->query('scope_type', 'global');
            $id = $request->filled('scope_id') ? (int) $request->query('scope_id') : null;
            if ($type === 'set' && $id) {
                $record = GraduationSet::findOrFail($id);
                return ['type' => 'set', 'id' => $record->id, 'label' => $record->name];
            }
            if ($type === 'branch' && $id) {
                $record = Branch::findOrFail($id);
                return ['type' => 'branch', 'id' => $record->id, 'label' => $record->name];
            }
            return ['type' => 'global', 'id' => null, 'label' => 'National Administration'];
        }

        $assignments = LeadershipAssignment::query()->where('user_id', $user->id)
            ->where('is_active', true)
            ->where(fn ($q) => $q->whereNull('starts_at')->orWhereDate('starts_at', '<=', today()))
            ->where(fn ($q) => $q->whereNull('ends_at')->orWhereDate('ends_at', '>=', today()))
            ->get(['scope_type', 'scope_id']);
        $set = $assignments->firstWhere('scope_type', 'set');
        if ($set) {
            $record = GraduationSet::findOrFail($set->scope_id);
            return ['type' => 'set', 'id' => $record->id, 'label' => $record->name];
        }
        $branch = $assignments->firstWhere('scope_type', 'branch');
        if ($branch) {
            $record = Branch::findOrFail($branch->scope_id);
            return ['type' => 'branch', 'id' => $record->id, 'label' => $record->name];
        }
        abort(403, 'You do not have permission to view membership reports.');
    }

    private function availableScopes($user): array
    {
        if (! $user->isAdmin()) return [];
        return [
            'sets' => GraduationSet::orderBy('graduation_year')->get(['id', 'name']),
            'branches' => Branch::orderBy('name')->get(['id', 'name']),
        ];
    }
}
