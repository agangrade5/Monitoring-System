<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class CheckSslCertificateJob implements ShouldQueue
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

        if (!$monitor->url || !str_starts_with($monitor->url, 'https://')) {
            return;
        }

        $host = parse_url($monitor->url, PHP_URL_HOST);

        if (!$host) {
            return;
        }

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
            10,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$socket) {
            $monitor->update([
                'ssl_status' => 'invalid',
            ]);

            return;
        }

        $params = stream_context_get_params($socket);

        fclose($socket);

        if (
            !isset($params['options']['ssl']['peer_certificate'])
        ) {
            $monitor->update([
                'ssl_status' => 'invalid',
            ]);

            return;
        }

        $certificate = openssl_x509_parse(
            $params['options']['ssl']['peer_certificate']
        );

        if (!$certificate || !isset($certificate['validTo_time_t'])) {
            $monitor->update([
                'ssl_status' => 'invalid',
            ]);

            return;
        }

        $expiresAt = now()->createFromTimestamp(
            $certificate['validTo_time_t']
        );

        $daysRemaining = now()->diffInDays($expiresAt, false);

        $status = match (true) {
            $daysRemaining < 0 => 'expired',
            $daysRemaining <= 7 => 'critical',
            $daysRemaining <= 30 => 'warning',
            default => 'valid',
        };

        $monitor->update([
            'ssl_enabled' => true,
            'ssl_expires_at' => $expiresAt,
            'ssl_days_remaining' => max(0, $daysRemaining),
            'ssl_status' => $status,
        ]);
    }
}