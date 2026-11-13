<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventCalendarController extends Controller
{
    public function index(Request $request)
    {
        $events = Event::query()
            ->where('published', true)
            ->whereNotNull('starts_at')
            ->where('starts_at', '>=', now()->subDays(30))
            ->orderBy('starts_at')
            ->get(['id', 'title', 'slug', 'starts_at', 'ends_at', 'venue', 'location', 'scope_type']);

        return Inertia::render('Events/Calendar', [
            'events' => $events,
            'calendarFeedUrl' => route('events.calendar.ics'),
        ]);
    }

    public function ics(Request $request)
    {
        $events = Event::query()
            ->where('published', true)
            ->whereNotNull('starts_at')
            ->where('starts_at', '>=', now()->subDays(30))
            ->orderBy('starts_at')
            ->get();

        $escape = static function (?string $value): string {
            return str_replace(["\\\\", ";", ",", "\r\n", "\n", "\r"], ["\\\\\\\\", "\\\\;", "\\\\,", "\\\\n", "\\\\n", ""], $value ?? '');
        };

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//BBHS Old Boys Association//Events//EN',
            'CALSCALE:GREGORIAN',
            'METHOD:PUBLISH',
        ];

        foreach ($events as $event) {
            $start = $event->starts_at->copy()->utc()->format('Ymd\\THis\\Z');
            $end = ($event->ends_at ?: $event->starts_at->copy()->addHour())->copy()->utc()->format('Ymd\\THis\\Z');
            $lines[] = 'BEGIN:VEVENT';
            $lines[] = 'UID:bbhs-event-' . $event->id . '@bbhs';
            $lines[] = 'DTSTAMP:' . now()->utc()->format('Ymd\\THis\\Z');
            $lines[] = 'DTSTART:' . $start;
            $lines[] = 'DTEND:' . $end;
            $lines[] = 'SUMMARY:' . $escape($event->title);
            $lines[] = 'DESCRIPTION:' . $escape(strip_tags($event->excerpt ?? $event->description ?? ''));
            $lines[] = 'LOCATION:' . $escape(trim(($event->venue ?? '') . ' ' . ($event->location ?? '')));
            $lines[] = 'URL:' . url('/events/' . $event->slug);
            $lines[] = 'END:VEVENT';
        }

        $lines[] = 'END:VCALENDAR';

        return response(implode("\r\n", $lines) . "\r\n", 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="bbhs-events.ics"',
        ]);
    }
}
