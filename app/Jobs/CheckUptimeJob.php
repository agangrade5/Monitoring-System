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
    /**
     * Create a new job instance.
     * 
     * @return void
     *  
     */ 
    public function __construct(
        protected int $monitorId
    ) {}
    /**
     * Execute the job.
     * 
     * @return void
     *  
     */
    public function handle(): void
    {
        $monitor = Monitor::with(['settings', 'checkResult'])->find($this->monitorId);

        if (!$monitor || !$monitor->is_active) {
            return;
        }

        if ($monitor->settings && !$monitor->settings->check_uptime) {
            return;
        }

        $checkedAt = now();
        $startTime = microtime(true);

        try {
            $response = Http::timeout(10)
                ->withoutVerifying()
                ->get($monitor->url);

            $responseTimeMs = max(1, (int) round((microtime(true) - $startTime) * 1000));

            $isHttpSuccess = $response->successful();

            // Refresh checkResult relation to ensure latest SSL & Domain status
            $monitor->load(['settings', 'checkResult']);

            // Check if SSL check is enabled and SSL certificate is expired or invalid
            $sslExpired = false;
            if ($monitor->settings?->check_ssl && in_array($monitor->checkResult?->ssl_status, ['expired', 'invalid'])) {
                $sslExpired = true;
            }

            // Check if Domain check is enabled and Domain is expired
            $domainExpired = false;
            if ($monitor->settings?->check_domain && $monitor->checkResult?->domain_status === 'expired') {
                $domainExpired = true;
            }

            if ($isHttpSuccess && !$sslExpired && !$domainExpired) {
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