<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Repositories\Contracts\MonitorLogRepositoryInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;

class CheckSslCertificateJob implements ShouldQueue
{
    use Queueable;
    /**
     * Create a new job instance.
     * 
     * @return void
     * @throws \Exception
     * 
     */
    public function __construct(
        protected int $monitorId
    ) {}
     /**
     * Execute the job.
     * 
     * @return void
     * @throws \Exception
     * 
     */
    public function handle(?MonitorLogRepositoryInterface $monitorLogRepository = null): void
    {
        $monitorLogRepository = $monitorLogRepository ?? app(MonitorLogRepositoryInterface::class);

        $monitor = Monitor::with('settings')->find($this->monitorId);
        if (!$monitor || !$monitor->is_active) {
            return;
        }

        if ($monitor->settings && !$monitor->settings->check_ssl) {
            return;
        }

        if (!$monitor->url || !str_starts_with($monitor->url, 'https://')) {
            return;
        }

        $startTime = microtime(true);
        $host = parse_url($monitor->url, PHP_URL_HOST);

        if (!$host) {
            return;
        }

        // 1. Perform strict cURL SSL verification (detects invalid CA, missing intermediate chain, host mismatch)
        $ch = curl_init($monitor->url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

      $caPath = storage_path('certs/cacert.pem');

        if (!is_file($caPath)) {
            $caPath = ini_get('curl.cainfo') ?: null;
        }

        if ($caPath && is_file($caPath)) {
            curl_setopt($ch, CURLOPT_CAINFO, $caPath);
        }
            

        curl_setopt($ch, CURLOPT_NOBODY, true);

        curl_exec($ch);
        $curlErrno = curl_errno($ch);
        $curlError = curl_error($ch);
        curl_close($ch);

        $isCaValid = ($curlErrno === 0);

        // 2. Connect with peer cert capture to parse details
        $context = stream_context_create([
            'ssl' => [
                'capture_peer_cert' => true,
                'verify_peer' => false,
                'verify_peer_name' => false,
            ],
        ]);

        $socket = @stream_socket_client(
            "ssl://{$host}:443",
            $errno,
            $errstr,
            5,
            STREAM_CLIENT_CONNECT,
            $context
        );

        $responseTimeMs = max(1, (int) round((microtime(true) - $startTime) * 1000));

        if (!$socket) {
            $monitor->checkResult()->updateOrCreate(['monitor_id' => $monitor->id], [
                'ssl_enabled' => true,
                'ssl_status' => 'invalid',
            ]);

            $monitor->update(['status' => 'down', 'last_down_at' => now()]);

            $monitorLogRepository->create([
                'monitor_id' => $monitor->id,
                'status' => 'down',
                'reason' => 'SSL socket connection failed',
                'http_status_code' => null,
                'response_time' => $responseTimeMs,
                'error_message' => $errstr ?: 'Unable to connect to port 443 with SSL',
                'request_body' => ['host' => $host, 'port' => 443],
                'response_body' => ['error_code' => $errno, 'error_message' => $errstr],
                'checked_at' => now(),
            ]);

            return;
        }

        $params = stream_context_get_params($socket);

        fclose($socket);

        if (
            !isset($params['options']['ssl']['peer_certificate'])
        ) {
            $monitor->checkResult()->updateOrCreate(['monitor_id' => $monitor->id], [
                'ssl_enabled' => true,
                'ssl_status' => 'invalid',
            ]);

            $monitor->update(['status' => 'down', 'last_down_at' => now()]);

            $monitorLogRepository->create([
                'monitor_id' => $monitor->id,
                'status' => 'down',
                'reason' => 'SSL peer certificate not found in stream',
                'http_status_code' => null,
                'response_time' => $responseTimeMs,
                'error_message' => 'No peer certificate returned by server',
                'request_body' => ['host' => $host, 'port' => 443],
                'response_body' => ['error' => 'No peer certificate returned by server'],
                'checked_at' => now(),
            ]);

            return;
        }

        $certificate = openssl_x509_parse(
            $params['options']['ssl']['peer_certificate']
        );

        if (!$certificate || !isset($certificate['validTo_time_t'])) {
            $monitor->checkResult()->updateOrCreate(['monitor_id' => $monitor->id], [
                'ssl_enabled' => true,
                'ssl_status' => 'invalid',
            ]);

            $monitor->update(['status' => 'down', 'last_down_at' => now()]);

            $monitorLogRepository->create([
                'monitor_id' => $monitor->id,
                'status' => 'down',
                'reason' => 'Unable to parse SSL certificate',
                'http_status_code' => null,
                'response_time' => $responseTimeMs,
                'error_message' => 'X.509 certificate parse failure',
                'request_body' => ['host' => $host, 'port' => 443],
                'response_body' => ['error' => 'X.509 certificate parse failure'],
                'checked_at' => now(),
            ]);

            return;
        }

        $expiresAt = now()->createFromTimestamp(
            $certificate['validTo_time_t']
        );

        $daysRemaining = (int) ceil(now()->diffInDays($expiresAt, false));

        $issuer = isset($certificate['issuer']) ? ($certificate['issuer']['O'] ?? $certificate['issuer']['CN'] ?? (is_array($certificate['issuer']) ? implode(', ', $certificate['issuer']) : $certificate['issuer'])) : null;

        $status = match (true) {
            !$isCaValid => 'invalid',
            $daysRemaining < 0 => 'expired',
            $daysRemaining <= 7 => 'critical',
            $daysRemaining <= 30 => 'warning',
            default => 'valid',
        };

        $monitor->checkResult()->updateOrCreate(['monitor_id' => $monitor->id], [
            'ssl_enabled' => true,
            'ssl_expires_at' => $expiresAt,
            'ssl_days_remaining' => max(0, $daysRemaining),
            'ssl_issuer' => $issuer,
            'ssl_status' => $status,
        ]);

        if (in_array($status, ['expired', 'invalid'])) {
            $monitor->update(['status' => 'down', 'last_down_at' => now()]);

            $monitorLogRepository->create([
                'monitor_id' => $monitor->id,
                'status' => 'down',
                'reason' => "SSL certificate is {$status} (Issuer: " . ($issuer ?? 'Unknown') . ")",
                'http_status_code' => null,
                'response_time' => $responseTimeMs,
                'error_message' => "SSL certificate expired on " . ($expiresAt ? $expiresAt->format('Y-m-d') : 'Unknown') . " (Days remaining: {$daysRemaining})" . (!$isCaValid && $curlError ? " | cURL Error: {$curlError}" : ''),
                'request_body' => ['host' => $host, 'port' => 443],
                'response_body' => [
                    'ssl_status' => $status,
                    'issuer' => $issuer,
                    'expires_at' => $expiresAt?->format('Y-m-d H:i:s'),
                    'days_remaining' => $daysRemaining,
                    'is_ca_valid' => $isCaValid,
                    'curl_error' => $curlError ?: null,
                ],
                'checked_at' => now(),
            ]);
        }
    }
}