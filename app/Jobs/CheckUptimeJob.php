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
        $startTime = microtime(true);

        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->get($monitor->url);

            $responseTimeMs = max(1, (int) round((microtime(true) - $startTime) * 1000));

            if ($response->successful()) {
                $monitor->update([
                    'status' => 'up',
                    'response_time' => $responseTimeMs,
                    'last_checked_at' => $checkedAt,
                    'last_up_at' => $checkedAt,
                ]);
            } else {
                $monitor->update([
                    'status' => 'down',
                    'response_time' => $responseTimeMs,
                    'last_checked_at' => $checkedAt,
                    'last_down_at' => $checkedAt,
                ]);
            }

        } catch (Throwable $e) {
            $responseTimeMs = max(1, (int) round((microtime(true) - $startTime) * 1000));

            $monitor->update([
                'status' => 'down',
                'response_time' => $responseTimeMs,
                'last_checked_at' => $checkedAt,
                'last_down_at' => $checkedAt,
            ]);
        }
    }
}