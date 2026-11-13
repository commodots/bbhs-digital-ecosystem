<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


// BBHS event reminders: notify registered participants 7, 3 and 1 day before events.
use Illuminate\Support\Facades\Schedule;

Schedule::command('bbhs:send-event-reminders')
    ->dailyAt('08:00')
    ->timezone('Africa/Lagos')
    ->withoutOverlapping();
