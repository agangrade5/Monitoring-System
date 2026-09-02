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

    public function __construct(
        public int $monitorId
    ) {}

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
                    'present' => $headers->has($key),
                    'value' => $headers->get($key),
                ];
            }

            // Example:
            // Save result to database here.

            $monitor->update([
                'security_headers' => $result,
            ]);

        } catch (Throwable $e) {
            report($e);
        }
    }
}