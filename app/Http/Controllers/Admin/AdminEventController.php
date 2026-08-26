<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminEventController extends Controller
{
    /**
     * Display all events.
     */
    public function index()
    {
        return Inertia::render('Admin/Events/Index', [
            'events' => Event::orderBy('starts_at')
                ->paginate(15),
        ]);
    }

    /**
     * Show create event form.
     */
    public function create()
    {
        return Inertia::render('Admin/Events/Form', [
            'event' => null,
        ]);
    }

    /**
     * Store a new event.
     */
    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['slug'] = $this->uniqueSlug($data['title']);

        $data['published'] = (bool) ($data['published'] ?? false);

        $data['registration_open'] =
            (bool) ($data['registration_open'] ?? false);

        Event::create($data);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    /**
     * Show edit event form.
     */
    public function edit(Event $event)
    {
        return Inertia::render('Admin/Events/Form', [
            'event' => $event,
        ]);
    }

    /**
     * Update an existing event.
     */
    public function update(
        Request $request,
        Event $event
    ) {
        $data = $this->validateData($request);

        $data['slug'] = $this->uniqueSlug(
            $data['title'],
            $event->id
        );

        $data['published'] =
            (bool) ($data['published'] ?? false);

        $data['registration_open'] =
            (bool) ($data['registration_open'] ?? false);

        $event->update($data);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    /**
     * Delete an event.
     */
    public function destroy(Event $event)
    {
        $event->delete();

        return back()->with(
            'success',
            'Event deleted successfully.'
        );
    }

    /**
     * Validate event data.
     */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => [
                'required',
                'string',
                'max:255',
            ],

            'excerpt' => [
                'nullable',
                'string',
                'max:500',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'image' => [
                'nullable',
                'string',
                'max:500',
            ],

            'starts_at' => [
                'nullable',
                'date',
            ],

            'ends_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],

            'venue' => [
                'nullable',
                'string',
                'max:255',
            ],

            'location' => [
                'nullable',
                'string',
                'max:255',
            ],

            'capacity' => [
                'nullable',
                'integer',
                'min:1',
            ],

            'registration_open' => [
                'nullable',
                'boolean',
            ],

            'published' => [
                'nullable',
                'boolean',
            ],
        ]);
    }

    /**
     * Generate a unique slug.
     */
    private function uniqueSlug(
        string $title,
        ?int $ignoreId = null
    ): string {
        $base = Str::slug($title);

        /*
        |--------------------------------------------------------------------------
        | Prevent empty slug
        |--------------------------------------------------------------------------
        */

        if (empty($base)) {
            $base = 'event';
        }

        $slug = $base;
        $counter = 1;

        while (
            Event::where('slug', $slug)
                ->when(
                    $ignoreId,
                    fn ($query) =>
                        $query->where('id', '!=', $ignoreId)
                )
                ->exists()
        ) {
            $slug = $base . '-' . $counter++;
        }

        return $slug;
    }
}