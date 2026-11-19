<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\News;
use App\Models\FundraisingCampaign;
use App\Models\User;
use App\Models\AlumniSpotlight;
use Illuminate\Support\Facades\DB;
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

        $campaigns = FundraisingCampaign::where('published', true)
            ->where('status', 'active')
            ->withCount(['donations as successful_donations_count' => fn ($q) => $q->where('status', 'success')])
            ->latest()->take(3)->get()
            ->map(function ($campaign) use ($image) {
                $target = (int) $campaign->target_amount_kobo;
                $raised = (int) $campaign->raised_amount_kobo;
                return [
                    'id' => $campaign->id, 'title' => $campaign->title, 'slug' => $campaign->slug,
                    'category' => $campaign->category ?: 'BBHS INITIATIVE', 'excerpt' => $campaign->excerpt,
                    'image' => $image($campaign->image, '/images/bbhs/school/bbhs-school-gate.jpg'),
                    'progress' => $target > 0 ? min(100, round($raised / $target * 100)) : 0,
                    'raised' => number_format($raised / 100, 0), 'target' => number_format($target / 100, 0),
                    'donors' => (int) $campaign->successful_donations_count,
                ];
            })->values();

        $spotlights = AlumniSpotlight::query()->with('alumniProfile.graduationSet')
            ->where('is_active', true)
            ->whereHas('alumniProfile', fn ($q) => $q->where('approved', true)->whereHas('user', fn ($u) => $u->where('member_status', 'approved')))
            ->orderBy('sort_order')->orderBy('id')->take(3)->get()->map(function ($spotlight) use ($image) {
                $profile = $spotlight->alumniProfile;
                return [
                    'id' => $spotlight->id,
                    'name' => trim($profile->first_name . ' ' . $profile->last_name),
                    'classSet' => $profile->graduationSet?->name ?? $profile->class_set ?? 'BBHS Old Boy',
                    'image' => $image($profile->photo, '/images/bbhs/alumni/default-alumni.jpg'),
                    'headline' => $spotlight->headline,
                    'writeup' => $spotlight->writeup,
                    'profileUrl' => '/alumni-directory/' . $profile->id,
                ];
            })->values();

        return Inertia::render('Home', [
            'home' => [
                'news' => $news,
                'events' => $events,
                'campaigns' => $campaigns,
                'spotlights' => $spotlights,
                'stats' => [
                    'alumni' => User::where('is_admin', false)->where('member_status', 'approved')->count(),
                    'countries' => User::where('is_admin', false)->where('member_status', 'approved')
                        ->whereHas('alumniProfile', fn ($q) => $q->whereNotNull('country')->where('country', '!=', ''))
                        ->with('alumniProfile:id,user_id,country')
                        ->get()->pluck('alumniProfile.country')->filter()->unique()->count(),
                    'events' => Event::where('published', true)->count(),
                    'news' => News::where('published', true)->count(),
                    'raised' => number_format((float) DB::table('fundraising_donations')->where('status', 'success')->sum('amount_kobo') / 100, 0),
                ],
            ],
        ]);
    }
}
