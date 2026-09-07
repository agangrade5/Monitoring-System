<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Throwable;

class CheckSecurityHeadersJob implements ShouldQueue
{
    use Queueable;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(
        public int $monitorId
    ) {}

    /**
     * Execute the job.
     * 
     * @return void
     * 
     */
    public function handle(): void
    {
        $monitor = Monitor::find($this->monitorId);

        if (!$monitor || !$monitor->is_active) {
            return;
        }
        try {
            $response = Http::timeout(15)
                ->withOptions([
                    'allow_redirects' => true,
                    'verify' => false,
                ])
                ->get($monitor->url);

            $headers = collect($response->headers())
                ->mapWithKeys(fn ($value, $key) => [
                    strtolower($key) => is_array($value)
                        ? $value[0]
                        : $value,
                ]);

            $securityHeaders = [
                'strict-transport-security' => 'Strict-Transport-Security',
                'content-security-policy' => 'Content-Security-Policy',
                'x-content-type-options' => 'X-Content-Type-Options',
                'x-frame-options' => 'X-Frame-Options',
                'referrer-policy' => 'Referrer-Policy',
                'permissions-policy' => 'Permissions-Policy',
            ];

            $result = [];

            foreach ($securityHeaders as $key => $name) {
                $result[$key] = [
                    'name' => $name,
                    'present' => $headers->has($key),
                    'value' => $headers->get($key),
                ];
            }

            $monitor->security_headers = $result;
            $monitor->save();

            \Log::info('Security Headers Saved', [
                'monitor_id' => $monitor->id,
                'security_headers' => $result,
            ]);

        } catch (Throwable $e) {

            \Log::error('Security Headers Job Failed', [
                'monitor_id' => $this->monitorId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}