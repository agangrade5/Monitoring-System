<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Repositories\Contracts\MonitorLogRepositoryInterface;
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
    public function handle(?MonitorLogRepositoryInterface $monitorLogRepository = null): void
    {
        $monitorLogRepository = $monitorLogRepository ?? app(MonitorLogRepositoryInterface::class);

        $monitor = Monitor::with([
            'settings',
            'checkResult',
        ])->find($this->monitorId);

        if (!$monitor || !$monitor->is_active) {
            return;
        }

        /*
         * Uptime check disabled for this monitor.
         */
        if ($monitor->settings && !$monitor->settings->check_uptime) {
            return;
        }

        $checkedAt = now();
        $startTime = microtime(true);

        $requestHeaders = [
            'User-Agent' => 'UptimeMonitor/1.0',
            'Accept' => '*/*',
            'Connection' => 'close',
        ];

        try {
            /*
             * Get local CA bundle.
             *
             * SSL verification is always enabled.
             */
            $caBundle = $this->getCaBundlePath();

            $http = Http::timeout(5)
                ->connectTimeout(5)
                ->withHeaders($requestHeaders)
                ->withOptions([
                    /*
                     * Follow redirects like a normal browser.
                     */
                    'allow_redirects' => [
                        'max' => 5,
                        'strict' => false,
                        'referer' => true,
                        'track_redirects' => true,
                    ],

                    /*
                     * IMPORTANT:
                     * SSL verification must remain enabled.
                     */
                    'verify' => $caBundle ?: true,
                ]);

            /*
             * Send HTTP/HTTPS request.
             */
            $response = $http->get($monitor->url);

            $responseTimeMs = max(
                1,
                (int) round(
                    (microtime(true) - $startTime) * 1000
                )
            );

            $httpStatusCode = $response->status();

            /*
             * UptimeRobot-style HTTP check:
             *
             * 2xx = UP
             * 3xx = UP if redirect completed / accepted
             * 4xx = DOWN
             * 5xx = DOWN
             */
            $isHttpSuccess = $httpStatusCode >= 200
                && $httpStatusCode < 400;

            /*
             * Refresh latest SSL/domain results.
             */
            $monitor->load([
                'settings',
                'checkResult',
            ]);

            /*
             * SSL status.
             */
            $sslDown = false;

            if (
                $monitor->settings?->check_ssl &&
                in_array(
                    $monitor->checkResult?->ssl_status,
                    [
                        'expired',
                        'invalid',
                    ],
                    true
                )
            ) {
                $sslDown = true;
            }

            /*
             * Domain expiry status.
             */
            $domainDown = false;

            if (
                $monitor->settings?->check_domain &&
                $monitor->checkResult?->domain_status === 'expired'
            ) {
                $domainDown = true;
            }

            /*
             * Final monitor status.
             *
             * Website is UP only when:
             *
             * HTTP is healthy
             * AND SSL is healthy
             * AND domain is not expired
             */
            $isHealthy = (
                $isHttpSuccess &&
                !$sslDown &&
                !$domainDown
            );

            $responseBodyData = $this->formatResponseBody($response);

            /*
             * =========================
             * WEBSITE UP
             * =========================
             */
            if ($isHealthy) {
                $monitor->update([
                    'status' => 'up',
                    'last_checked_at' => $checkedAt,
                    'last_up_at' => $checkedAt,
                ]);

                return;
            }

            /*
             * =========================
             * WEBSITE DOWN
             * =========================
             */

            $reason = match (true) {
                $sslDown =>
                    'SSL certificate is expired or invalid',

                $domainDown =>
                    'Domain registration is expired',

                !$isHttpSuccess =>
                    'HTTP request failed with status code ' . $httpStatusCode,

                default =>
                    'Website health check failed',
            };

            $monitor->update([
                'status' => 'down',
                'last_checked_at' => $checkedAt,
                'last_down_at' => $checkedAt,
            ]);

            $monitorLogRepository->create([
                'monitor_id' => $monitor->id,
                'status' => 'down',
                'reason' => $reason,
                'http_status_code' => $httpStatusCode,
                'response_time' => $responseTimeMs,
                'error_message' => $reason,

                'request_body' => [
                    'method' => 'GET',
                    'url' => $monitor->url,
                    'headers' => $requestHeaders,
                ],

                'response_body' => $responseBodyData,

                'checked_at' => $checkedAt,
            ]);

        } catch (Throwable $e) {

            $responseTimeMs = max(
                1,
                (int) round(
                    (microtime(true) - $startTime) * 1000
                )
            );

            $reason = $this->getFailureReason($e);

            $monitor->update([
                'status' => 'down',
                'last_checked_at' => $checkedAt,
                'last_down_at' => $checkedAt,
            ]);

            $monitorLogRepository->create([
                'monitor_id' => $monitor->id,
                'status' => 'down',
                'reason' => $reason,
                'http_status_code' => null,
                'response_time' => $responseTimeMs,
                'error_message' => $e->getMessage(),

                'request_body' => [
                    'method' => 'GET',
                    'url' => $monitor->url,
                    'headers' => $requestHeaders,
                ],

                'response_body' => [
                    'error' => $e->getMessage(),
                    'trace' => mb_substr(
                        $e->getTraceAsString(),
                        0,
                        500
                    ),
                ],

                'checked_at' => $checkedAt,
            ]);
        }
    }

    /**
     * Format response body for JSON storage.
     */
    private function formatResponseBody($response): array
    {
        $contentType = $response->header('Content-Type') ?? '';
        $rawBody = $response->body();

        if (
            str_contains(
                strtolower($contentType),
                'application/json'
            )
        ) {
            $json = $response->json();

            if ($json !== null) {
                return [
                    'type' => 'json',
                    'content_type' => $contentType,
                    'data' => $json,
                ];
            }
        }

        return [
            'type' => 'raw',
            'content_type' => $contentType,
            'size_bytes' => strlen($rawBody),
            'preview' => mb_substr($rawBody, 0, 1000),
        ];
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
     * Get readable reason for failed request.
     */
    private function getFailureReason(Throwable $e): string
    {
        $message = strtolower($e->getMessage());

        /*
         * SSL / Certificate errors
         */
        if (
            str_contains($message, 'ssl') ||
            str_contains($message, 'certificate') ||
            str_contains($message, 'cert') ||
            str_contains($message, 'certificate verify failed') ||
            str_contains($message, 'unable to get local issuer certificate') ||
            str_contains($message, 'self signed certificate') ||
            str_contains($message, 'authority invalid') ||
            str_contains($message, 'curl error 60')
        ) {
            return 'SSL certificate verification failed';
        }

        /*
         * Timeout
         */
        if (
            str_contains($message, 'timed out') ||
            str_contains($message, 'timeout')
        ) {
            return 'Connection timeout';
        }

        /*
         * DNS
         */
        if (
            str_contains($message, 'could not resolve host') ||
            str_contains($message, 'name or service not known') ||
            str_contains($message, 'dns')
        ) {
            return 'DNS resolution failed';
        }

        /*
         * Connection refused
         */
        if (
            str_contains($message, 'connection refused')
        ) {
            return 'Connection refused';
        }

        /*
         * Connection reset
         */
        if (
            str_contains($message, 'connection reset')
        ) {
            return 'Connection reset by server';
        }

        return 'Connection / Request failed';
    }
}