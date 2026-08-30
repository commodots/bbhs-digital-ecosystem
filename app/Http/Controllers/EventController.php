<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Inertia\Inertia;

class EventController extends Controller
{
    public function index()
    {
        return Inertia::render('Events', [
            'events' => Event::where('published', true)
                ->orderBy('starts_at')
                ->paginate(12)
                ->through(fn ($event) => [
                    'id' => $event->id,
                    'title' => $event->title,
                    'slug' => $event->slug,
                    'excerpt' => $event->excerpt,
                    'image' => $event->image,
                    'starts_at' => optional($event->starts_at)->format('Y-m-d H:i:s'),
                    'date' => optional($event->starts_at)->format('d'),
                    'month' => optional($event->starts_at)->format('M'),
                    'time' => optional($event->starts_at)->format('g:i A'),
                    'venue' => $event->venue,
                    'location' => $event->location,
                    'capacity' => $event->capacity,
                    'registration_open' => $event->registration_open,
                ]),
        ]);
    }

    public function show(Event $event)
    {
        abort_unless($event->published, 404);

        return Inertia::render('EventDetail', [
            'event' => [
                'id' => $event->id,
                'title' => $event->title,
                'slug' => $event->slug,
                'excerpt' => $event->excerpt,
                'description' => $event->description,
                'image' => $event->image,
                'starts_at' => optional($event->starts_at)->format('M d, Y g:i A'),
                'ends_at' => optional($event->ends_at)->format('M d, Y g:i A'),
                'venue' => $event->venue,
                'location' => $event->location,
                'capacity' => $event->capacity,
                'registration_open' => $event->registration_open,
            ],
        ]);
    }
}
