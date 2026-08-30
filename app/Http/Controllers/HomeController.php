<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\News;
use App\Models\Project;
use App\Models\User;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $image = function (?string $path, string $fallback): string {
            if (!$path) return $fallback;
            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) return $path;
            return '/storage/' . ltrim($path, '/');
        };

        $news = News::where('published', true)
            ->orderByDesc('published_at')
            ->orderByDesc('created_at')
            ->take(3)
            ->get()
            ->map(fn ($item) => [
                'id' => $item->id,
                'title' => $item->title,
                'slug' => $item->slug,
                'category' => $item->category ?: 'NEWS',
                'excerpt' => $item->excerpt,
                'image' => $image($item->image, '/images/bbhs/events/reunion-01.jpg'),
                'date' => optional($item->published_at ?? $item->created_at)->format('M d, Y'),
            ])->values();

        $events = Event::where('published', true)
            ->whereNotNull('starts_at')
            ->where('starts_at', '>=', now())
            ->orderBy('starts_at')
            ->take(3)
            ->get()
            ->map(fn ($event) => [
                'id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'day' => optional($event->starts_at)->format('d'),
                'month' => optional($event->starts_at)->format('M'),
                'time' => optional($event->starts_at)->format('g:i A'),
                'date' => optional($event->starts_at)->format('M d, Y'),
                'location' => $event->location ?: $event->venue,
            ])->values();

        $projects = Project::where('published', true)
            ->whereIn('status', ['active', 'ongoing'])
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($project) use ($image) {
                $target = (float) $project->target_amount;
                $raised = (float) $project->raised_amount;
                $progress = $target > 0 ? min(100, round(($raised / $target) * 100)) : 0;
                return [
                    'id' => $project->id,
                    'title' => $project->title,
                    'slug' => $project->slug,
                    'category' => $project->category ?: 'PROJECT',
                    'excerpt' => $project->excerpt,
                    'image' => $image($project->image, '/images/bbhs/school/bbhs-school-gate.jpg'),
                    'status' => $project->status,
                    'progress' => $progress,
                    'raised' => number_format($raised, 0),
                    'target' => number_format($target, 0),
                ];
            })->values();

        return Inertia::render('Home', [
            'home' => [
                'news' => $news,
                'events' => $events,
                'projects' => $projects,
                'stats' => [
                    'alumni' => User::where('is_admin', false)->where('member_status', 'approved')->count(),
                    'countries' => User::where('is_admin', false)->where('member_status', 'approved')
                        ->whereHas('alumniProfile', fn ($q) => $q->whereNotNull('country')->where('country', '!=', ''))
                        ->with('alumniProfile:id,user_id,country')
                        ->get()->pluck('alumniProfile.country')->filter()->unique()->count(),
                    'events' => Event::where('published', true)->count(),
                    'news' => News::where('published', true)->count(),
                    'raised' => number_format((float) Project::where('published', true)->sum('raised_amount'), 0),
                ],
            ],
        ]);
    }
}
