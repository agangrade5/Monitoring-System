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

            $monitor->security_headers = $result;
            $monitor->security_grade = $grade;
            $monitor->save();

            \Log::info('Security Headers Saved', [
                'monitor_id' => $monitor->id,
                'grade' => $grade,
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