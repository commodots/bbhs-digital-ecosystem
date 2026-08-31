<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminEventController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Events/Index', [
            'events' => Event::latest()->paginate(15),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Events/Form', [
            'event' => null,
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);

        $data['slug'] = $this->uniqueSlug($data['title']);

        Event::create($data);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        return Inertia::render('Admin/Events/Form', [
            'event' => $event,
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $data = $this->validateData($request);

        $data['slug'] = $this->uniqueSlug(
            $data['title'],
            $event->id
        );

        $event->update($data);

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
        $event->delete();

        return back()->with(
            'success',
            'Event deleted successfully.'
        );
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],

            'excerpt' => ['nullable', 'string', 'max:500'],

            'description' => ['nullable', 'string'],

            'image' => ['nullable', 'string', 'max:500'],

            'starts_at' => [
                'required_if:published,1',
                'nullable',
                'date',
            ],

            'ends_at' => [
                'nullable',
                'date',
                'after_or_equal:starts_at',
            ],

            'venue' => ['nullable', 'string', 'max:255'],

            'location' => ['nullable', 'string', 'max:255'],

            'capacity' => ['nullable', 'integer', 'min:1'],

            'registration_open' => ['nullable', 'boolean'],

            'published' => ['nullable', 'boolean'],
        ]);
    }

    private function uniqueSlug(
        string $title,
        ?int $ignoreId = null
    ): string {
        $base = Str::slug($title);

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