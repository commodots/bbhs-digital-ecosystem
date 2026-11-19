<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class MemberLoginController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        if (! Auth::attempt(
            [
                'email' => $credentials['email'],
                'password' => $credentials['password'],
            ],
            $request->boolean('remember')
        )) {
            throw ValidationException::withMessages([
                'email' => 'The email or password is incorrect.',
            ]);
        }

        $request->session()->regenerate();
        $user = $request->user();

        // Super Admins retain access to the full administration dashboard.
        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return redirect('/admin/dashboard');
        }
        // Set and Branch executives use the scoped verification workspace.
        // Do not send them to the global admin dashboard, which contains
        // organisation-wide information intended for Super Admins.
        $hasActiveExecutiveAssignment = $user->leadershipAssignments()
            ->where('is_active', true)
            ->where(function ($query) {
                $query->whereNull('starts_at')
                    ->orWhereDate('starts_at', '<=', now()->toDateString());
            })
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhereDate('ends_at', '>=', now()->toDateString());
            })
            ->exists();

        if ($hasActiveExecutiveAssignment) {
            return redirect('/admin/verifications');
        }

        return redirect('/dashboard');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
