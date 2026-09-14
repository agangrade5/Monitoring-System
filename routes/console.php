<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\Monitor;
use App\Jobs\CheckUptimeJob;
use App\Jobs\CheckSslCertificateJob;
use App\Jobs\CheckDomainExpiryJob;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Monitor Commands
|--------------------------------------------------------------------------
*/

Artisan::command('monitors:check-uptime', function () {
    $monitors = Monitor::where('is_active', true)
        ->where(function ($query) {
            $query->whereDoesntHave('settings')
                ->orWhereHas('settings', fn($q) => $q->where('check_uptime', true));
        })
        ->pluck('id');

    $this->info("Dispatching uptime checks for {$monitors->count()} active monitors...");
    foreach ($monitors as $monitorId) {
        CheckUptimeJob::dispatch($monitorId);
    }
    $this->info('All uptime check jobs dispatched to queue.');
})->purpose('Dispatch uptime check jobs for active monitors');


Artisan::command('monitors:check-ssl', function () {
    $monitors = Monitor::where('is_active', true)
        ->where(function ($query) {
            $query->whereDoesntHave('settings')
                ->orWhereHas('settings', fn($q) => $q->where('check_ssl', true));
        })
        ->pluck('id');

    $this->info("Dispatching SSL certificate checks for {$monitors->count()} active monitors...");
    foreach ($monitors as $monitorId) {
        CheckSslCertificateJob::dispatch($monitorId);
    }
    $this->info('All SSL check jobs dispatched to queue.');
})->purpose('Dispatch SSL check jobs for active monitors');


Artisan::command('monitors:check-domain', function () {
    $monitors = Monitor::where('is_active', true)
        ->where(function ($query) {
            $query->whereDoesntHave('settings')
                ->orWhereHas('settings', fn($q) => $q->where('check_domain', true));
        })
        ->pluck('id');

    $this->info("Dispatching domain expiry checks for {$monitors->count()} active monitors...");
    foreach ($monitors as $monitorId) {
        CheckDomainExpiryJob::dispatch($monitorId);
    }
    $this->info('All domain expiry check jobs dispatched to queue.');
})->purpose('Dispatch domain expiry check jobs for active monitors');

/*
|--------------------------------------------------------------------------
| Scheduled Tasks
|--------------------------------------------------------------------------
*/

// Website Uptime: Every 5 Minutes
Schedule::command('monitors:check-uptime')->everyFiveMinutes()->withoutOverlapping();

// SSL Certificate: Daily
Schedule::command('monitors:check-ssl')->daily()->withoutOverlapping();

// Domain Expiry: Daily
Schedule::command('monitors:check-domain')->daily()->withoutOverlapping();

