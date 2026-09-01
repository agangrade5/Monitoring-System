<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Throwable;

class CheckDomainExpiryJob implements ShouldQueue
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

        $host = parse_url($monitor->url, PHP_URL_HOST);

        if (!$host) {
            return;
        }

        // www.example.com -> example.com
        $domain = preg_replace('/^www\./i', '', $host);

        try {
            $response = Http::timeout(15)
                ->acceptJson()
                ->get("https://rdap.org/domain/{$domain}");

            if (!$response->successful()) {
                $monitor->update([
                    'domain_status' => 'unknown',
                    'domain_checked_at' => now(),
                    'domain_expires_at' => null,
                ]);

                return;
            }

            $data = $response->json();

            $expiryDate = $this->getExpiryDate($data);

            if (!$expiryDate) {
                $monitor->update([
                    'domain_status' => 'unknown',
                    'domain_checked_at' => now(),
                     'domain_expires_at' => $expiryDate,
                ]);

                return;
            }

            $expiry = now()->parse($expiryDate);

            $monitor->update([
                'domain_expires_at' => $expiry,
                'domain_status' => $expiry->isPast() ? 'expired' : 'active',
                'domain_checked_at' => now(),
            ]);

        } catch (Throwable $e) {
            report($e);

            $monitor->update([
                'domain_status' => 'unknown',
                'domain_checked_at' => now(),
                
            ]);
        }
    }

    private function getExpiryDate(array $data): ?string
    {
        foreach ($data['events'] ?? [] as $event) {
            if (($event['eventAction'] ?? null) === 'expiration') {
                return $event['eventDate'] ?? null;
            }
        }

        return null;
    }
}