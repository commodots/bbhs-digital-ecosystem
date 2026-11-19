<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;

/**
 * Production-safe historical event content.
 * Only seeds the Founders' Day event with a date stated in the 2025 brochure.
 * No RSVP, attendance, or attendee records are fabricated.
 */
class BbhsHistoricalEventsSeeder extends Seeder
{
    public function run(): void
    {
        Event::updateOrCreate(
            ['slug' => 'bbhs-102nd-founders-day-2025'],
            [
                'title' => 'BBHS 102nd Founders’ Day Celebration',
                'excerpt' => 'The Association’s 102nd Founders’ Day was held on Saturday, 18 January 2025.',
                'description' => "Historical BBHS Founders’ Day event. The 2025 Association brochure identifies the 102nd Founders’ Day as having been held on Saturday, 18 January 2025. This record is retained as a historical event; no attendance figures or programme details are inferred.",
                'image' => null,
                'starts_at' => '2025-01-18 00:00:00',
                'ends_at' => '2025-01-18 23:59:59',
                'venue' => null,
                'location' => null,
                'capacity' => null,
                'registration_open' => false,
                'published' => true,
                'scope_type' => 'global',
                'graduation_set_id' => null,
                'branch_id' => null,
                'created_by' => null,
                'requires_registration' => false,
                'allow_non_branch_residents' => true,
            ]
        );

        $this->command?->info('BBHS historical event seeded (1 record).');
        $this->command?->warn('No upcoming event was created: the current source material contains no verified future event date, venue or schedule.');
    }
}
