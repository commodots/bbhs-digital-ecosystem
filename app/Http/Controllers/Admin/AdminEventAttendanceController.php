<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventAttendance;
use App\Models\EventMinute;
use App\Models\EventResolution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminEventAttendanceController extends Controller
{
    public function show(Event $event)
    {
        $registrations = $event->registrations()
            ->with(['user:id,name,email', 'user.alumniProfile'])
            ->where('status', 'registered')->orderBy('created_at')->get();

        // Ensure every confirmed registrant has an attendance row.
        foreach ($registrations as $registration) {
            EventAttendance::firstOrCreate(
                ['event_id' => $event->id, 'user_id' => $registration->user_id],
                ['status' => 'absent']
            );
        }

        $attendances = EventAttendance::with('user:id,name,email')
            ->where('event_id', $event->id)->orderBy('status')->orderBy('id')->get();

        return Inertia::render('Admin/Events/Attendance', [
            'event' => $event,
            'attendances' => $attendances,
            'minutes' => $event->minutes,
            'resolutions' => $event->resolutions()->latest()->get(),
            'stats' => [
                'registered' => $registrations->count(),
                'present' => $attendances->where('status', 'present')->count(),
                'absent' => $attendances->where('status', 'absent')->count(),
                'excused' => $attendances->where('status', 'excused')->count(),
            ],
        ]);
    }

    public function updateAttendance(Request $request, Event $event, EventAttendance $attendance)
    {
        abort_unless($attendance->event_id === $event->id, 404);
        $data = $request->validate([
            'status' => ['required', 'in:present,absent,excused'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $data['marked_by'] = $request->user()->id;
        $data['checked_in_at'] = $data['status'] === 'present' ? now() : null;
        $attendance->update($data);

        return back()->with('success', 'Attendance updated.');
    }

    public function saveMinutes(Request $request, Event $event)
    {
        $data = $request->validate([
            'summary' => ['nullable', 'string', 'max:5000'],
            'minutes' => ['nullable', 'string', 'max:50000'],
            'recorded_by_name' => ['nullable', 'string', 'max:255'],
        ]);

        $event->minutes()->updateOrCreate(['event_id' => $event->id], $data);
        return back()->with('success', 'Event minutes saved.');
    }

    public function storeResolution(Request $request, Event $event)
    {
        $data = $request->validate([
            'resolution' => ['required', 'string', 'max:5000'],
            'responsible_person' => ['nullable', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'status' => ['required', 'in:open,in_progress,completed'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $event->resolutions()->create($data);
        return back()->with('success', 'Resolution added.');
    }

    public function updateResolution(Request $request, Event $event, EventResolution $resolution)
    {
        abort_unless($resolution->event_id === $event->id, 404);
        $data = $request->validate([
            'status' => ['required', 'in:open,in_progress,completed'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $resolution->update($data);
        return back()->with('success', 'Resolution updated.');
    }

    public function exportAttendance(Event $event)
    {
        $rows = EventAttendance::with('user:id,name,email')
            ->where('event_id', $event->id)->orderBy('id')->get();

        return response()->streamDownload(function () use ($rows, $event) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Event', 'Member', 'Email', 'Attendance', 'Checked In At', 'Notes']);
            foreach ($rows as $row) {
                fputcsv($out, [$event->title, $row->user?->name, $row->user?->email, $row->status, optional($row->checked_in_at)->format('Y-m-d H:i:s'), $row->notes]);
            }
            fclose($out);
        }, 'bbhs-event-attendance-' . $event->id . '.csv', ['Content-Type' => 'text/csv']);
    }
}
