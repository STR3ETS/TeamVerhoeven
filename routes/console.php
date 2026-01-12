<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Scheduler: Verwijder clients waarvan abonnement meer dan 1 dag verlopen is.
 * Dit command draait dagelijks om 02:00 's nachts.
 */
Schedule::command('users:purge-expired')
    ->dailyAt('02:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/purge-expired-users.log'));
