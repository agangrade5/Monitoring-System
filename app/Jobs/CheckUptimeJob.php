<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Throwable;

class CheckUptimeJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected int $monitorId
    ) {}

    public function handle(): void
    {
        $monitor = Monitor::find($this->monitorId);

        if (!$monitor || !$monitor->is_active) {
            return;
        }

        $checkedAt = now();

        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->get($monitor->url);

            if ($response->successful()) {
                $monitor->update([
                    'status' => 'up',
                    'last_checked_at' => $checkedAt,
                    'last_up_at' => $checkedAt,
                ]);
            } else {
                $monitor->update([
                    'status' => 'down',
                    'last_checked_at' => $checkedAt,
                    'last_down_at' => $checkedAt,
                ]);
            }

        } catch (Throwable $e) {

            $monitor->update([
                'status' => 'down',
                'last_checked_at' => $checkedAt,
                'last_down_at' => $checkedAt,
            ]);
        }
    }
}