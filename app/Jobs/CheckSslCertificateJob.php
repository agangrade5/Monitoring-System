<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

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
    public function handle(): void
    {
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

        $caPath = ini_get('curl.cainfo') ?: (file_exists(storage_path('cacert.pem')) ? storage_path('cacert.pem') : null);
        if ($caPath && file_exists($caPath)) {
            curl_setopt($ch, CURLOPT_CAINFO, $caPath);
        }

        curl_setopt($ch, CURLOPT_NOBODY, true);

        curl_exec($ch);
        $curlErrno = curl_errno($ch);
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

        if (!$socket) {
            $monitor->checkResult()->updateOrCreate(['monitor_id' => $monitor->id], [
                'ssl_enabled' => true,
                'ssl_status' => 'invalid',
            ]);

            $monitor->update(['status' => 'down', 'last_down_at' => now()]);

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
        }
    }
}