<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminMemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->with('alumniProfile')
            ->where('is_admin', false);

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('member_status', $request->string('status'));
        }

        $members = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Admin/Members/Index', [
            'members' => $members,
            'filters' => $request->only(['search', 'status']),
        ]);
    }

    public function show(User $user)
    {
        abort_if($user->is_admin, 404);
        $user->load('alumniProfile');

        return Inertia::render('Admin/Members/Show', [
            'member' => $user,
        ]);
    }

    public function approve(User $user)
    {
        abort_if($user->is_admin, 404);

        $user->update(['member_status' => 'approved']);

        $user->alumniProfile()->updateOrCreate(
            ['user_id' => $user->id],
            ['approved' => true, 'directory_visible' => true]
        );

        return back()->with('success', 'Member approved and added to the alumni directory.');
    }

    public function suspend(User $user)
    {
        abort_if($user->is_admin, 404);

        $user->update(['member_status' => 'suspended']);

        if ($user->alumniProfile) {
            $user->alumniProfile->update([
                'approved' => false,
                'directory_visible' => false,
            ]);
        }

        return back()->with('success', 'Member suspended and hidden from the alumni directory.');
    }

    public function reject(User $user)
    {
        abort_if($user->is_admin, 404);

        $user->update(['member_status' => 'rejected']);

        if ($user->alumniProfile) {
            $user->alumniProfile->update([
                'approved' => false,
                'directory_visible' => false,
            ]);
        }

        return back()->with('success', 'Member application rejected.');
    }
}
