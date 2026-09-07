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

    /**
     *  Create a new job instance.
     * 
     *  @return void
     * 
     * @throws \Exception
     */
    public function __construct(
        public int $monitorId
    ) {}
    /**
     * Execute the job.
     * 
     * @return void
     * 
     * @throws \Exception
     */
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

        $host = strtolower($host);

        // Remove www.
        $host = preg_replace('/^www\./i', '', $host);

        /*
        * Subdomain -> Main domain
        *
        * app.example.com     -> example.com
        * api.example.com     -> example.com
        * www.example.com     -> example.com
        * example.com         -> example.com
        */
        $parts = explode('.', $host);

        if (count($parts) >= 2) {
            $domain = implode('.', array_slice($parts, -2));
        } else {
            $domain = $host;
        }

        try {

            $response = Http::timeout(15)
                ->acceptJson()
                ->withoutVerifying()
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
                    'domain_expires_at' => null,
                ]);

                return;
            }

            $expiry = \Carbon\Carbon::parse($expiryDate);

            $monitor->update([
                'domain_expires_at' => $expiry,
                'domain_status' => $expiry->isPast()
                    ? 'expired'
                    : 'active',
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
    /**
     * Get the expiry date from the RDAP response.
     * 
     * @param array $data
     * 
     * @return string|null
     * 
     * @throws \Exception
     * 
     */
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