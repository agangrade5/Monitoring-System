<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http; // <-- YE LINE MISSING THI

class CheckPhpVersionJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $monitorId
    ) {}

    public function handle(): void
    {
        $monitor = Monitor::find($this->monitorId);

        if (!$monitor || !$monitor->url) {
            return;
        }

        try {
            $response = Http::timeout(15)->withoutVerifying()->get($monitor->url);

            $phpVersion = $this->extractPhpVersion($response);
            $wpVersion  = $this->extractWordPressVersion($response->body());

            $monitor->update([
                'php_version'    => $phpVersion ?: 'Unknown',
                'php_status'     => $phpVersion ? 'up' : 'unknown',
                'wp_version'     => $wpVersion ?: null, // agar column ho to
                'php_checked_at' => now(),
            ]);

        } catch (\Throwable $e) {
            report($e);

            $monitor->update([
                'php_version'    => 'Unknown',
                'php_status'     => 'unknown',
                'php_checked_at' => now(),
            ]);
        }
    }

    private function extractPhpVersion($response): ?string
    {
        $poweredBy = $response->header('X-Powered-By');
        if ($poweredBy && preg_match('/PHP\/([\d.]+)/i', $poweredBy, $m)) {
            return $m[1];
        }

        $server = $response->header('Server');
        if ($server && preg_match('/PHP\/([\d.]+)/i', $server, $m)) {
            return $m[1];
        }

        return null;
    }

    private function extractWordPressVersion(string $html): ?string
    {
        if (preg_match('/<meta\s+name="generator"\s+content="WordPress\s+([\d.]+)"/i', $html, $m)) {
            return $m[1];
        }

        if (preg_match('/generator=WordPress\/([\d.]+)/i', $html, $m)) {
            return $m[1];
        }

        return null;
    }

}