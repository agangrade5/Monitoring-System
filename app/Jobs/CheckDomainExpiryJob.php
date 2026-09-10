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
        $monitor = Monitor::with('settings')->find($this->monitorId);

        if (!$monitor || !$monitor->is_active || !$monitor->url) {
            return;
        }

        if ($monitor->settings && !$monitor->settings->check_domain) {
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

            $response = Http::timeout(5)
                ->acceptJson()
                ->withoutVerifying()
                ->get("https://rdap.org/domain/{$domain}");

            if (!$response->successful()) {
                $monitor->checkResult()->updateOrCreate(['monitor_id' => $monitor->id], [
                    'domain_status' => 'unknown',
                    'domain_checked_at' => now(),
                    'domain_expires_at' => null,
                ]);

                return;
            }

            $data = $response->json();

            $expiryDate = $this->getExpiryDate($data);
            $registrar = $this->getRegistrar($data);

            if (!$expiryDate) {
                $monitor->checkResult()->updateOrCreate(['monitor_id' => $monitor->id], [
                    'domain_status' => 'unknown',
                    'domain_checked_at' => now(),
                    'domain_expires_at' => null,
                    'domain_registrar' => $registrar,
                ]);

                return;
            }

            $expiry = \Carbon\Carbon::parse($expiryDate);
            $daysRemaining = (int) ceil(now()->diffInDays($expiry, false));

            $domainStatus = match (true) {
                $daysRemaining < 0 => 'expired',
                $daysRemaining <= 30 => 'warning',
                default => 'active',
            };

            $monitor->checkResult()->updateOrCreate(['monitor_id' => $monitor->id], [
                'domain_expires_at' => $expiry,
                'domain_days_remaining' => max(0, $daysRemaining),
                'domain_registrar' => $registrar,
                'domain_status' => $domainStatus,
                'domain_checked_at' => now(),
            ]);

            if ($domainStatus === 'expired') {
                $monitor->update(['status' => 'down', 'last_down_at' => now()]);
            }

        } catch (Throwable $e) {

            report($e);

            $monitor->checkResult()->updateOrCreate(['monitor_id' => $monitor->id], [
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

    /**
     * Get the registrar name from the RDAP response.
     * 
     * @param array $data
     * 
     * @return string|null
     */
    private function getRegistrar(array $data): ?string
    {
        foreach ($data['entities'] ?? [] as $entity) {
            if (in_array('registrar', $entity['roles'] ?? [])) {
                if (isset($entity['vcardArray'][1]) && is_array($entity['vcardArray'][1])) {
                    foreach ($entity['vcardArray'][1] as $vc) {
                        if (is_array($vc) && isset($vc[0]) && $vc[0] === 'fn' && isset($vc[3])) {
                            return $vc[3];
                        }
                    }
                }
                if (!empty($entity['fn'])) {
                    return $entity['fn'];
                }
            }
        }

        return null;
    }
}