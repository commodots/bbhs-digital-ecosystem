<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\News;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        return Inertia::render('Admin/Dashboard', [
            'admin' => $request->user(),
            'stats' => [
                'members' => User::where('is_admin', false)->count(),
                'pending_members' => User::where('is_admin', false)
                    ->where('member_status', 'pending')
                    ->count(),
                'news' => News::count(),
                'published_news' => News::where('published', true)->count(),
                'events' => Event::count(),
                'upcoming_events' => Event::where('published', true)
                    ->whereNotNull('starts_at')
                    ->where('starts_at', '>=', now())
                    ->count(),
                'projects' => Project::count(),
                'published_projects' => Project::where('published', true)->count(),
            ],
            'recentMembers' => User::where('is_admin', false)
                ->latest()
                ->take(5)
                ->get(['id','name','email','member_status']),
            'upcomingEvents' => Event::where('published', true)
                ->whereNotNull('starts_at')
                ->where('starts_at', '>=', now())
                ->orderBy('starts_at')
                ->take(5)
                ->get(['id','title','starts_at','venue']),
            'recentNews' => News::latest()
                ->take(5)
                ->get(['id','title','published','published_at']),
        ]);
    }
}
