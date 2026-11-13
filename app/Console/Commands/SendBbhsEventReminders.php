<?php

namespace App\Console\Commands;

use App\Models\Event;
use App\Notifications\BbhsPlatformNotification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SendBbhsEventReminders extends Command
{
    protected $signature = 'bbhs:send-event-reminders';
    protected $description = 'Send once-only 7, 3 and 1 day reminders to registered BBHS event participants.';

    public function handle(): int
    {
        $today = now()->startOfDay();
        $sent = 0;

        foreach ([7, 3, 1] as $days) {
            $targetDate = $today->copy()->addDays($days);

            $events = Event::query()
                ->where('published', true)
                ->whereNotNull('starts_at')
                ->whereDate('starts_at', $targetDate->toDateString())
                ->with(['registrations.user'])
                ->get();

            foreach ($events as $event) {
                foreach ($event->registrations->where('status', 'registered') as $registration) {
                    $user = $registration->user;
                    if (! $user) {
                        continue;
                    }

                    $inserted = DB::table('event_reminder_logs')->insertOrIgnore([
                        'event_id' => $event->id,
                        'user_id' => $user->id,
                        'reminder_days' => $days,
                        'sent_at' => now(),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    if (! $inserted) {
                        continue;
                    }

                    $date = $event->starts_at->format('l, j F Y \a\t g:i A');
                    $user->notify(new BbhsPlatformNotification(
                        "Event reminder: {$event->title}",
                        "This is your {$days}-day reminder. {$event->title} is scheduled for {$date}.",
                        'event_reminder',
                        '/events/' . $event->slug,
                        ['event_id' => $event->id, 'reminder_days' => $days]
                    ));
                    $sent++;
                }
            }
        }

        $this->info("Sent {$sent} event reminder notification(s).");
        return self::SUCCESS;
    }
}
