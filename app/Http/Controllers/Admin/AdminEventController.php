<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\GraduationSet;
use App\Models\Branch;
use App\Models\EventScope;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminEventController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Events/Index', [
            'events' => Event::withCount(['registrations as registered_count' => fn ($q) => $q->where('status', 'registered')])->latest()->paginate(15),
        ]);
    }

    public function registrations(Event $event)
    {
        $registrations = $event->registrations()
            ->with('user:id,name,email')
            ->where('status', 'registered')
            ->latest()
            ->paginate(30);

        return Inertia::render('Admin/Events/Registrations', [
            'event' => $event,
            'registrations' => $registrations,
            'registeredCount' => $event->registrations()->where('status', 'registered')->count(),
            'cancelledCount' => $event->registrations()->where('status', 'cancelled')->count(),
        ]);
    }

    public function exportRegistrations(Event $event)
    {
        $rows = $event->registrations()
            ->with('user:id,name,email')
            ->where('status', 'registered')
            ->oldest()
            ->get();

        return response()->streamDownload(function () use ($rows, $event) {
            $output = fopen('php://output', 'w');
            fputcsv($output, ['Event', 'Member Name', 'Email', 'Registration Date']);
            foreach ($rows as $registration) {
                fputcsv($output, [
                    $event->title,
                    $registration->user?->name ?? '',
                    $registration->user?->email ?? '',
                    optional($registration->created_at)->format('Y-m-d H:i:s'),
                ]);
            }
            fclose($output);
        }, 'bbhs-event-registrations-' . $event->id . '.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Events/Form', [
            'event' => null,
            'sets' => GraduationSet::where('is_active', true)->orderBy('graduation_year')->get(['id', 'name', 'graduation_year']),
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'selectedScopes' => [],
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image_file')) {
            $data['image'] = $request->file('image_file')->store('events', 'public');
        }
        unset($data['image_file']);
        $targetScopes = $data['target_scopes'] ?? [];
        unset($data['target_scopes']);

        $event = DB::transaction(function () use ($data, $targetScopes, $request) {
            $data['slug'] = $this->uniqueSlug($data['title']);
            $data['created_by'] = $request->user()->id;
            $event = Event::create($data);
            $this->syncScopes($event, $targetScopes);
            return $event;
        });

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully.');
    }

    public function edit(Event $event)
    {
        return Inertia::render('Admin/Events/Form', [
            'event' => $event,
            'sets' => GraduationSet::where('is_active', true)->orderBy('graduation_year')->get(['id', 'name', 'graduation_year']),
            'branches' => Branch::where('is_active', true)->orderBy('name')->get(['id', 'name']),
            'selectedScopes' => $event->scopes()->get(['scope_type', 'scope_id'])
                ->map(fn ($scope) => $scope->scope_type . ':' . $scope->scope_id)->values(),
        ]);
    }

    public function update(Request $request, Event $event)
    {
        $data = $this->validateData($request);
        if ($request->hasFile('image_file')) {
            if ($event->image && !filter_var($event->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image_file')->store('events', 'public');
        }
        unset($data['image_file']);
        $targetScopes = $data['target_scopes'] ?? [];
        unset($data['target_scopes']);

        DB::transaction(function () use ($event, $data, $targetScopes) {
            $data['slug'] = $this->uniqueSlug($data['title'], $event->id);
            $event->update($data);
            $this->syncScopes($event, $targetScopes);
        });

        return redirect()->route('admin.events.index')
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
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:500'],
            'image_file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'starts_at' => ['required_if:published,1', 'nullable', 'date'],
            'ends_at' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'venue' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'registration_open' => ['nullable', 'boolean'],
            'published' => ['nullable', 'boolean'],
            'requires_registration' => ['nullable', 'boolean'],
            'allow_non_branch_residents' => ['nullable', 'boolean'],
            'scope_type' => ['required', 'in:global,set,branch,joint'],
            'graduation_set_id' => ['nullable', 'required_if:scope_type,set', 'exists:graduation_sets,id'],
            'branch_id' => ['nullable', 'required_if:scope_type,branch', 'exists:branches,id'],
            'target_scopes' => ['nullable', 'array'],
            'target_scopes.*' => ['string', 'regex:/^(set|branch):[0-9]+$/'],
        ]);

        if ($data['scope_type'] === 'joint' && empty($data['target_scopes'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'target_scopes' => 'Select at least one graduation set or branch for a joint event.',
            ]);
        }

        if ($data['scope_type'] !== 'set') $data['graduation_set_id'] = null;
        if ($data['scope_type'] !== 'branch') $data['branch_id'] = null;
        if ($data['scope_type'] !== 'joint') $data['target_scopes'] = [];

        return $data;
    }

    private function syncScopes(Event $event, array $targets): void
    {
        $event->scopes()->delete();

        if ($event->scope_type === 'joint') {
            foreach (array_unique($targets) as $target) {
                [$type, $id] = explode(':', $target, 2);
                $exists = $type === 'set'
                    ? GraduationSet::whereKey($id)->exists()
                    : Branch::whereKey($id)->exists();

                if ($exists) {
                    $event->scopes()->create([
                        'scope_type' => $type,
                        'scope_id' => (int) $id,
                    ]);
                }
            }
        }
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