<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http; // <-- YE LINE MISSING THI

class CheckPhpVersionJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     * 
     * @return void
     * @throws \Throwable
     * @throws \Exception
     * 
     */
    public function __construct(
        public int $monitorId
    ) {}
    /**
     * Execute the job.
     * 
     * @return void
     * @throws \Throwable
     * @throws \Exception
     * 
     */
    public function handle(): void
    {
        $monitor = Monitor::with('settings')->find($this->monitorId);
        if (!$monitor || !$monitor->is_active || !$monitor->url) {
            return;
        }

        if ($monitor->settings && !$monitor->settings->check_php) {
            return;
        }

        try {
            $response = Http::timeout(15)->withoutVerifying()->get($monitor->url);

            $phpVersion = $this->extractPhpVersion($response);
            $wpVersion  = $this->extractWordPressVersion($response->body());

            $monitor->checkResult()->updateOrCreate(['monitor_id' => $monitor->id], [
                'php_version'    => $phpVersion ?: 'Unknown',
                'php_status'     => $phpVersion ? 'up' : 'unknown',
                'php_checked_at' => now(),
            ]);

        } catch (\Throwable $e) {
            report($e);

            $monitor->checkResult()->updateOrCreate(['monitor_id' => $monitor->id], [
                'php_version'    => 'Unknown',
                'php_status'     => 'unknown',
                'php_checked_at' => now(),
            ]);
        }
    }
    /**
     * Extract the PHP version from the response headers.
     * 
     * @param \Illuminate\Http\Client\Response $response
     * 
     * @return string|null
     * 
     */
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

     /**
      * Extract the WordPress version from the HTML content.
      * 
      * @param string $html
      *
      * @return string|null
      */
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