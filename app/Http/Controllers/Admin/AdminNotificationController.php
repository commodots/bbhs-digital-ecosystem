<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\GraduationSet;
use App\Models\User;
use App\Notifications\BbhsPlatformNotification;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminNotificationController extends Controller
{
    public function create(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        return Inertia::render('Admin/Notifications/Create', [
            'sets' => GraduationSet::where('is_active', true)->orderBy('graduation_year')->get(['id', 'name']),
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(['id', 'name']),
        ]);
    }

    public function store(Request $request)
    {
        abort_unless($request->user()->isAdmin(), 403);

        $data = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:3000'],
            'category' => ['required', 'in:announcement,event,membership,general'],
            'audience' => ['required', 'in:all,members,administrators,set,branch'],
            'scope_id' => ['nullable', 'integer'],
        ]);

        if ($data['audience'] === 'set') {
            $request->validate(['scope_id' => ['required', 'integer', 'exists:graduation_sets,id']]);
        } elseif ($data['audience'] === 'branch') {
            $request->validate(['scope_id' => ['required', 'integer', 'exists:branches,id']]);
        }

        $users = User::query();
        switch ($data['audience']) {
            case 'members':
                $users->where('role', 'member');
                break;
            case 'administrators':
                $users->where(fn ($q) => $q->where('is_admin', true)->orWhere('role', 'admin'));
                break;
            case 'set':
                $users->where(fn ($q) => $q
                    ->whereHas('alumniProfile', fn ($profile) => $profile->where('graduation_set_id', $data['scope_id']))
                    ->orWhereHas('leadershipAssignments', fn ($assignment) => $assignment->where('scope_type', 'set')->where('scope_id', $data['scope_id'])));
                break;
            case 'branch':
                $users->where(fn ($q) => $q
                    ->whereHas('branchAffiliations', fn ($affiliation) => $affiliation
                        ->where('branch_id', $data['scope_id'])->where('status', 'approved'))
                    ->orWhereHas('leadershipAssignments', fn ($assignment) => $assignment
                        ->where('scope_type', 'branch')->where('scope_id', $data['scope_id'])));
                break;
        }

        $count = 0;
        $notification = new BbhsPlatformNotification(
            $data['title'],
            $data['message'],
            $data['category'],
            '/notifications',
            ['sent_by' => $request->user()->id, 'audience' => $data['audience'], 'scope_id' => $data['scope_id'] ?? null],
        );

        $users->select('users.*')->orderBy('users.id')->chunkById(200, function ($recipients) use ($notification, &$count) {
            foreach ($recipients as $recipient) {
                $recipient->notify($notification);
                $count++;
            }
        });

        return redirect()->route('admin.notifications.create')
            ->with('success', "Notification sent to {$count} user(s).");
    }
}
