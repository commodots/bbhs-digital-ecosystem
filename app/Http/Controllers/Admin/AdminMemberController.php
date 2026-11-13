<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;
use Inertia\Inertia;

class AdminMemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query()
            ->with('alumniProfile.graduationSet')
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
        $user->load('alumniProfile.graduationSet');

        return Inertia::render('Admin/Members/Show', [
            'member' => $user,
        ]);
    }

    /**
     * The legacy global approval button must not bypass mandatory set approval.
     * The authoritative approval action is the scoped verification queue.
     */
    public function updateEmail(Request $request, User $user)
    {
        abort_if($user->is_admin, 404);
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ]);

        $newEmail = strtolower(trim($validated['email']));
        $emailChanged = strtolower($user->email) !== $newEmail;
        $user->forceFill([
            'email' => $newEmail,
            'email_verified_at' => $emailChanged ? null : $user->email_verified_at,
        ])->save();

        return back()->with('success', 'Member email address updated successfully.');
    }

    /** Update the member's core profile details from the admin member page. */
    public function updateProfile(Request $request, User $user)
    {
        abort_if($user->is_admin, 404);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:40'],
        ]);

        $firstName = trim($validated['first_name']);
        $lastName = trim($validated['last_name']);

        $user->forceFill(['name' => trim($firstName . ' ' . $lastName)])->save();
        $user->alumniProfile()->updateOrCreate(
            ['user_id' => $user->id],
            ['first_name' => $firstName, 'last_name' => $lastName, 'phone' => trim($validated['phone'])]
        );

        return back()->with('success', 'Member name and phone number updated successfully.');
    }

    /**
     * Update or remove a member's profile photograph.
     * Uses the same public storage location as the member self-service profile.
     */
    public function updatePhoto(Request $request, User $user)
    {
        abort_if($user->is_admin, 404);

        $validated = $request->validate([
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_profile_photo' => ['sometimes', 'boolean'],
        ]);

        $profile = $user->alumniProfile;
        $currentPhoto = $profile?->photo;

        if ($request->boolean('remove_profile_photo')) {
            if ($currentPhoto) {
                Storage::disk('public')->delete($currentPhoto);
            }

            $user->alumniProfile()->updateOrCreate(
                ['user_id' => $user->id],
                ['photo' => null]
            );

            return back()->with('success', 'Member profile photograph removed successfully.');
        }

        if ($request->hasFile('profile_photo')) {
            $newPhoto = $request->file('profile_photo')->store('member-photos', 'public');

            $user->alumniProfile()->updateOrCreate(
                ['user_id' => $user->id],
                ['photo' => $newPhoto]
            );

            if ($currentPhoto && $currentPhoto !== $newPhoto) {
                Storage::disk('public')->delete($currentPhoto);
            }

            return back()->with('success', 'Member profile photograph updated successfully.');
        }

        return back()->with('info', 'Choose a photograph or select Remove Photograph.');
    }

    public function sendPasswordSetupLink(User $user)
    {
        abort_if($user->is_admin, 404);

        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('success', 'Password setup/recovery link sent to ' . $user->email . '.');
        }

        return back()->with('error', __($status));
    }

    public function approve(User $user)
    {
        abort_if($user->is_admin, 404);

        return redirect()->route('admin.verifications.index')
            ->with('info', 'Membership must be approved by the member’s graduation set President or Secretary. Use the Pending Verifications page.');
    }

    public function suspend(User $user)
    {
        abort_if($user->is_admin, 404);

        $user->forceFill(['member_status' => 'suspended'])->save();

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

        $user->forceFill(['member_status' => 'rejected'])->save();

        if ($user->alumniProfile) {
            $user->alumniProfile->update([
                'approved' => false,
                'directory_visible' => false,
            ]);
        }

        return back()->with('success', 'Member application rejected by global administration.');
    }
}
