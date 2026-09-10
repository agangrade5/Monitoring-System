<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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
        $monitor = Monitor::with('settings')->find($this->monitorId);

        if (!$monitor || !$monitor->is_active) {
            return;
        }

        if ($monitor->settings && !$monitor->settings->check_security_headers) {
            return;
        }
        try {
            $url = $monitor->url;
            if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                $url = "http://" . $url;
            }

            $response = Http::timeout(15)
                ->withOptions([
                    'allow_redirects' => true,
                    'verify' => false,
                ])
                ->get($url);

            $headers = collect($response->headers())
                ->mapWithKeys(fn ($value, $key) => [
                    strtolower($key) => is_array($value)
                        ? $value[0]
                        : $value,
                ]);

            $securityHeaders = [
                'strict-transport-security' => [
                    'name' => 'Strict-Transport-Security (HSTS)',
                    'description' => 'Forces secure HTTPS connections and prevents SSL stripping attacks.',
                ],
                'content-security-policy' => [
                    'name' => 'Content-Security-Policy (CSP)',
                    'description' => 'Mitigates Cross-Site Scripting (XSS) and malicious data injection.',
                ],
                'x-frame-options' => [
                    'name' => 'X-Frame-Options',
                    'description' => 'Prevents Clickjacking by controlling iframe embedding.',
                ],
                'x-content-type-options' => [
                    'name' => 'X-Content-Type-Options',
                    'description' => 'Blocks MIME-type sniffing to prevent malicious script execution.',
                ],
                'referrer-policy' => [
                    'name' => 'Referrer-Policy',
                    'description' => 'Controls referrer information sent in HTTP requests.',
                ],
                'permissions-policy' => [
                    'name' => 'Permissions-Policy',
                    'description' => 'Restricts browser permissions (Camera, Geolocation, Microphone).',
                ],
            ];

            $result = [];

            foreach ($securityHeaders as $key => $meta) {
                $result[$key] = [
                    'name' => $meta['name'],
                    'description' => $meta['description'],
                    'present' => $headers->has($key),
                    'value' => $headers->get($key),
                ];
            }

            $presentCount = collect($result)->where('present', true)->count();

            $grade = match(true) {
                $presentCount === 6 => 'A+',
                $presentCount >= 5 => 'A',
                $presentCount >= 4 => 'B',
                $presentCount >= 3 => 'C',
                $presentCount >= 2 => 'D',
                default => 'F',
            };

            $monitor->checkResult()->updateOrCreate(['monitor_id' => $monitor->id], [
                'security_headers' => $result,
                'security_grade' => $grade,
            ]);
        } catch (Throwable $e) {
            Log::channel('monitoring')->error('Security Headers Job Failed', [
                'monitor_id' => $this->monitorId,
                'error' => $e->getMessage(),
            ]);

            $defaultHeaders = [
                'strict-transport-security' => ['name' => 'Strict-Transport-Security (HSTS)', 'description' => 'Forces secure HTTPS connections.', 'present' => false, 'value' => null],
                'content-security-policy' => ['name' => 'Content-Security-Policy (CSP)', 'description' => 'Mitigates XSS.', 'present' => false, 'value' => null],
                'x-frame-options' => ['name' => 'X-Frame-Options', 'description' => 'Prevents Clickjacking.', 'present' => false, 'value' => null],
                'x-content-type-options' => ['name' => 'X-Content-Type-Options', 'description' => 'Blocks MIME sniffing.', 'present' => false, 'value' => null],
                'referrer-policy' => ['name' => 'Referrer-Policy', 'description' => 'Controls referrer information.', 'present' => false, 'value' => null],
                'permissions-policy' => ['name' => 'Permissions-Policy', 'description' => 'Restricts permissions.', 'present' => false, 'value' => null],
            ];

            $monitor->checkResult()->updateOrCreate(['monitor_id' => $monitor->id], [
                'security_headers' => $defaultHeaders,
                'security_grade' => 'F',
            ]);
        }
    }
}