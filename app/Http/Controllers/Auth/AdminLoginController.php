<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminLoginController extends Controller
{
    public function create()
    {
        return \Inertia\Inertia::render('AdminLogin');
    }

    public function store(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'The email or password is incorrect.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();
        $user = $request->user();

        if (method_exists($user, 'isAdmin') && $user->isAdmin()) {
            return redirect('/admin/dashboard');
        }
        if ($user->welfare_team) {
            return redirect('/admin/welfare');
        }

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

        Auth::logout();

        return back()->withErrors([
            'email' => 'This account does not have administrator or active executive access.',
        ])->onlyInput('email');
    }

    public function destroy(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
