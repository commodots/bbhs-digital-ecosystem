<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class MemberDashboardController extends Controller
{
    public function index()
    {
        $user = request()->user()->load('alumniProfile');

        return Inertia::render('Dashboard', [
            'member' => $user,
            'alumniProfile' => $user->alumniProfile,
        ]);
    }
}
