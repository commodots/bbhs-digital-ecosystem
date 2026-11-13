<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MemberProfileController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->user()->load('alumniProfile.graduationSet');

        return Inertia::render('Profile', [
            'alumniProfile' => $user->alumniProfile,
            'photoUrl' => $user->alumniProfile?->photo ? Storage::disk('public')->url($user->alumniProfile->photo) : null,
        ]);
    }

    public function update(Request $request)
    {
        $user = $request->user();
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['required', 'string', 'max:40'],
            'whatsapp_phone' => ['nullable', 'string', 'max:40'],
            'alternate_phone' => ['nullable', 'string', 'max:40'],
            'preferred_contact' => ['required', 'in:whatsapp,sms,email,phone'],
            'sms_consent' => ['sometimes', 'boolean'],
            'whatsapp_consent' => ['sometimes', 'boolean'],
            'profession' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:150'],
            'country' => ['nullable', 'string', 'max:150'],
            'bio' => ['nullable', 'string', 'max:3000'],
            'directory_visible' => ['sometimes', 'boolean'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'remove_profile_photo' => ['sometimes', 'boolean'],
        ]);

        $city = isset($validated['city']) ? trim($validated['city']) : null;
        $country = isset($validated['country']) ? trim($validated['country']) : null;

        $profileData = [
            'first_name' => trim($validated['first_name']),
            'last_name' => trim($validated['last_name']),
            'phone' => $validated['phone'],
            'whatsapp_phone' => $validated['whatsapp_phone'] ?? null,
            'alternate_phone' => $validated['alternate_phone'] ?? null,
            'preferred_contact' => $validated['preferred_contact'],
            'sms_consent' => (bool)($validated['sms_consent'] ?? false),
            'whatsapp_consent' => (bool)($validated['whatsapp_consent'] ?? false),
            'profession' => $validated['profession'] ?? null,
            'city' => $city,
            'city_normalized' => $city !== null && $city !== '' ? Str::lower($city) : null,
            'country' => $country,
            'country_normalized' => $country !== null && $country !== '' ? Str::lower($country) : null,
            'bio' => $validated['bio'] ?? null,
        ];

        if (array_key_exists('directory_visible', $validated)) {
            $profileData['directory_visible'] = (bool) $validated['directory_visible'];
        }

        // Graduation set is intentionally immutable here; changes require an association verification workflow.
        $user->update(['name' => $profileData['first_name'] . ' ' . $profileData['last_name']]);
        $alumniProfile = $user->alumniProfile;
        if ($request->boolean('remove_profile_photo') && $alumniProfile?->photo) {
            Storage::disk('public')->delete($alumniProfile->photo);
            $profileData['photo'] = null;
        }
        if ($request->hasFile('profile_photo')) {
            if ($alumniProfile?->photo) {
                Storage::disk('public')->delete($alumniProfile->photo);
            }
            $profileData['photo'] = $request->file('profile_photo')->store('member-photos', 'public');
        }
        $user->alumniProfile()->updateOrCreate(['user_id' => $user->id], $profileData);

        return back()->with('success', 'Profile updated successfully.');
    }
}
