<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\AlumniProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class MemberRegistrationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required','string','max:100'],
            'last_name' => ['required','string','max:100'],
            'email' => ['required','email','max:255','unique:users,email'],
            'phone' => ['nullable','string','max:40'],
            'class_set' => ['nullable','string','max:100'],
            'city' => ['nullable','string','max:100'],
            'country' => ['nullable','string','max:100'],
            'password' => ['required','confirmed',Password::defaults()],
        ]);

        $user = User::create([
            'name' => trim($validated['first_name'].' '.$validated['last_name']),
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'member',
            'member_status' => 'pending',
        ]);

        AlumniProfile::create([
            'user_id' => $user->id,
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'phone' => $validated['phone'] ?? null,
            'class_set' => $validated['class_set'] ?? null,
            'city' => $validated['city'] ?? null,
            'country' => $validated['country'] ?? null,
        ]);

        return redirect('/login')->with('success', 'Registration completed. Your membership is pending approval.');
    }
}
