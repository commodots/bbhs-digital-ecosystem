<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class MemberProfileController extends Controller
{
    /**
     * Display the logged-in member's profile.
     */
    public function show(Request $request)
    {
        $user = $request->user()->load('alumniProfile');

        return Inertia::render('Profile', [
            'alumniProfile' => $user->alumniProfile,
        ]);
    }

    /**
     * Update the logged-in member's profile.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'phone' => ['nullable', 'string', 'max:40'],
            'class_set' => ['nullable', 'string', 'max:100'],
            'profession' => ['nullable', 'string', 'max:150'],
            'city' => ['nullable', 'string', 'max:100'],
            'country' => ['nullable', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:3000'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Update user's main account name
        |--------------------------------------------------------------------------
        */

        $user->update([
            'name' => trim(
                $validated['first_name'] . ' ' . $validated['last_name']
            ),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Create or update alumni profile
        |--------------------------------------------------------------------------
        */

        $user->alumniProfile()->updateOrCreate(
            ['user_id' => $user->id],
            $validated
        );

        /*
        |--------------------------------------------------------------------------
        | Return to profile
        |--------------------------------------------------------------------------
        */

        return back()->with(
            'success',
            'Profile updated successfully.'
        );
    }
}