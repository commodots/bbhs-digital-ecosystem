<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class WelfareTeamController extends Controller
{
    public function index()
    {
        $candidates = User::query()
            ->where('member_status', 'approved')
            ->whereHas('alumniProfile', fn ($q) => $q->where('approved', true))
            ->with(['alumniProfile.graduationSet'])
            ->orderBy('name')->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'set' => $user->alumniProfile?->graduationSet?->name ?? $user->alumniProfile?->class_set,
                'positions' => [],
                'selected' => (bool) $user->welfare_team,
            ])->values();

        return Inertia::render('Admin/Welfare/Team', ['candidates' => $candidates]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'user_ids' => ['present', 'array'],
            'user_ids.*' => ['integer', 'distinct', 'exists:users,id'],
        ]);

        $eligibleIds = User::whereIn('id', $data['user_ids'])
            ->where('member_status', 'approved')
            ->whereHas('alumniProfile', fn ($q) => $q->where('approved', true))
            ->pluck('id')->all();

        abort_if(count($eligibleIds) !== count($data['user_ids']), 422, 'Only approved BBHS members can be appointed to the Welfare Team.');

        DB::transaction(function () use ($eligibleIds) {
            User::where('welfare_team', true)->update(['welfare_team' => false]);
            if ($eligibleIds) User::whereIn('id', $eligibleIds)->update(['welfare_team' => true]);
        });

        return back()->with('success', 'Welfare Team appointments updated.');
    }
}
