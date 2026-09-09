<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Models\MonitorLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Throwable;

class CheckUptimeJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected int $monitorId
    ) {}

    /**
     * Execute the job.
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
            $caBundle = $this->getCaBundlePath();

            $http = Http::timeout(5);
            if ($caBundle) {
                $http = $http->withOptions(['verify' => $caBundle]);
            } else {
                $http = $http->withoutVerifying();
            }

            try {
                $response = $http->get($monitor->url);
            } catch (Throwable $e) {
                // If local PHP cURL SSL authority check fails (cURL Error 60), retry withoutVerifying to test HTTP reachability
                if ($caBundle && str_contains(strtolower($e->getMessage()), 'curl error 60')) {
                    $response = Http::timeout(5)->withoutVerifying()->get($monitor->url);
                } else {
                    throw $e;
                }
            }

            $responseTimeMs = max(
                1,
                (int) round((microtime(true) - $startTime) * 1000)
            );

            $httpStatusCode = $response->status();
            $isHttpSuccess = $response->successful();

            // Refresh checkResult relation to evaluate SSL & Domain status
            $monitor->load(['settings', 'checkResult']);

            $sslExpired = false;
            if ($monitor->settings?->check_ssl && in_array($monitor->checkResult?->ssl_status, ['expired', 'invalid'])) {
                $sslExpired = true;
            }

            $domainExpired = false;
            if ($monitor->settings?->check_domain && $monitor->checkResult?->domain_status === 'expired') {
                $domainExpired = true;
            }

            $isHealthy = $isHttpSuccess && !$sslExpired && !$domainExpired;

            if ($isHealthy) {
                // Website is UP and healthy
                $monitor->update([
                    'status' => 'up',
                    'response_time' => $responseTimeMs,
                    'last_checked_at' => $checkedAt,
                    'last_up_at' => $checkedAt,
                ]);

                MonitorLog::create([
                    'monitor_id' => $monitor->id,
                    'status' => 'up',
                    'reason' => 'Website is reachable',
                    'http_status_code' => $httpStatusCode,
                    'response_time' => $responseTimeMs,
                    'error_message' => null,
                    'checked_at' => $checkedAt,
                ]);
            } else {
                // Website is DOWN due to HTTP failure, Invalid/Expired SSL, or Expired Domain
                $reason = match (true) {
                    $sslExpired => 'SSL certificate is expired or invalid (Authority untrusted / missing chain)',
                    $domainExpired => 'Domain registration is expired',
                    !$isHttpSuccess => 'HTTP request failed with status code ' . $httpStatusCode,
                    default => 'Website health check failed',
                };

                $monitor->update([
                    'status' => 'down',
                    'response_time' => $responseTimeMs,
                    'last_checked_at' => $checkedAt,
                    'last_down_at' => $checkedAt,
                ]);

                MonitorLog::create([
                    'monitor_id' => $monitor->id,
                    'status' => 'down',
                    'reason' => $reason,
                    'http_status_code' => $httpStatusCode,
                    'response_time' => $responseTimeMs,
                    'error_message' => $reason,
                    'checked_at' => $checkedAt,
                ]);
            }

        } catch (Throwable $e) {
            $responseTimeMs = max(
                1,
                (int) round((microtime(true) - $startTime) * 1000)
            );

            $reason = $this->getFailureReason($e);

            $monitor->update([
                'status' => 'down',
                'response_time' => $responseTimeMs,
                'last_checked_at' => $checkedAt,
                'last_down_at' => $checkedAt,
            ]);

            MonitorLog::create([
                'monitor_id' => $monitor->id,
                'status' => 'down',
                'reason' => $reason,
                'http_status_code' => null,
                'response_time' => $responseTimeMs,
                'error_message' => $e->getMessage(),
                'checked_at' => $checkedAt,
            ]);
        }
    }

    /**
     * Get local CA bundle filepath if available.
     */
    private function getCaBundlePath(): string|bool
    {
        $iniCurl = ini_get('curl.cainfo');
        if ($iniCurl && file_exists($iniCurl)) {
            return $iniCurl;
        }

        $iniOpenSsl = ini_get('openssl.cafile');
        if ($iniOpenSsl && file_exists($iniOpenSsl)) {
            return $iniOpenSsl;
        }

        $xamppCa = 'C:\xampp\perl\vendor\lib\Mozilla\CA\cacert.pem';
        if (file_exists($xamppCa)) {
            return $xamppCa;
        }

        return false;
    }

    /**
     * Get a readable reason for the failed request.
     */
    private function getFailureReason(Throwable $e): string
    {
        $message = strtolower($e->getMessage());

        // SSL / Certificate errors
        if (
            str_contains($message, 'ssl') ||
            str_contains($message, 'certificate') ||
            str_contains($message, 'cert') ||
            str_contains($message, 'certificate verify failed') ||
            str_contains($message, 'unable to get local issuer certificate') ||
            str_contains($message, 'self signed certificate') ||
            str_contains($message, 'authority invalid')
        ) {
            return 'SSL certificate verification failed';
        }

        // Connection timeout
        if (
            str_contains($message, 'timed out') ||
            str_contains($message, 'timeout')
        ) {
            return 'Connection timeout';
        }

        // DNS errors
        if (
            str_contains($message, 'could not resolve host') ||
            str_contains($message, 'name or service not known') ||
            str_contains($message, 'dns')
        ) {
            return 'DNS resolution failed';
        }

        // Connection refused
        if (
            str_contains($message, 'connection refused')
        ) {
            return 'Connection refused';
        }

        // Connection reset
        if (
            str_contains($message, 'connection reset')
        ) {
            return 'Connection reset by server';
        }

        return 'Connection / Request failed';
    }
}