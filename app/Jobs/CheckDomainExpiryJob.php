<?php

namespace App\Jobs;

use App\Models\Monitor;
use App\Models\MonitorLog;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Spatie\Rdap\Facades\Rdap;
use Throwable;

class CheckDomainExpiryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public int $monitorId
    ) {
    }

    public function handle(): void
    {
        Log::info('Domain expiry check started.', [
            'monitor_id' => $this->monitorId,
        ]);

        try {
            /*
             * ---------------------------------------------------------
             * Get monitor
             * ---------------------------------------------------------
             */
            $monitor = Monitor::with('settings')->find($this->monitorId);

            if (!$monitor) {
                Log::warning('Monitor not found.', [
                    'monitor_id' => $this->monitorId,
                ]);

                return;
            }

            /*
             * ---------------------------------------------------------
             * Monitor active check
             * ---------------------------------------------------------
             */
            if (!$monitor->is_active || empty($monitor->url)) {
                Log::info('Domain expiry check skipped.', [
                    'monitor_id' => $monitor->id,
                    'reason' => 'Monitor inactive or URL missing.',
                ]);

                return;
            }

            /*
             * ---------------------------------------------------------
             * Domain check enabled?
             * ---------------------------------------------------------
             */
            if (
                $monitor->settings &&
                !$monitor->settings->check_domain
            ) {
                Log::info('Domain expiry check disabled.', [
                    'monitor_id' => $monitor->id,
                ]);

                return;
            }

            /*
             * ---------------------------------------------------------
             * Extract hostname
             * ---------------------------------------------------------
             */
            $host = parse_url(
                $monitor->url,
                PHP_URL_HOST
            );

            /*
             * If URL does not contain scheme
             *
             * Example:
             * example.com
             */
            if (!$host) {
                $host = parse_url(
                    'https://' . ltrim($monitor->url, '/'),
                    PHP_URL_HOST
                );
            }

            if (!$host) {
                $this->saveUnknownResult(
                    $monitor,
                    'Unable to extract hostname from URL.'
                );

                return;
            }

            $host = strtolower(
                trim($host, '.')
            );

            /*
             * Remove www.
             *
             * www.example.com
             *       ↓
             * example.com
             */
            $host = preg_replace(
                '/^www\./i',
                '',
                $host
            );

            /*
             * ---------------------------------------------------------
             * Get registrable/main domain
             * ---------------------------------------------------------
             *
             * app.example.com
             *       ↓
             * example.com
             *
             * app.example.co.in
             *       ↓
             * example.co.in
             */
            $domain = $this->getRegistrableDomain($host);

            if (!$domain) {
                $this->saveUnknownResult(
                    $monitor,
                    'Unable to determine registrable domain.'
                );

                return;
            }

            Log::info('Domain resolved.', [
                'monitor_id' => $monitor->id,
                'host' => $host,
                'domain' => $domain,
            ]);

            /*
             * ---------------------------------------------------------
             * RDAP query
             * ---------------------------------------------------------
             *
             * Spatie automatically determines the appropriate
             * RDAP server for the domain TLD.
             */
            try {
                $domainResponse = Rdap::domain($domain);
            } catch (Throwable $e) {
                $this->saveUnknownResult(
                    $monitor,
                    "RDAP request failed for {$domain}: {$e->getMessage()}"
                );

                return;
            }

            /*
             * ---------------------------------------------------------
             * Domain not found / unsupported
             * ---------------------------------------------------------
             */
            if (!$domainResponse) {
                $this->saveUnknownResult(
                    $monitor,
                    "RDAP returned no data for {$domain}."
                );

                return;
            }

            /*
             * ---------------------------------------------------------
             * Get expiry date
             * ---------------------------------------------------------
             */
            $expiry = $domainResponse->expirationDate();

            if (!$expiry) {
                $this->saveUnknownResult(
                    $monitor,
                    'Expiration date not available from RDAP.'
                );

                return;
            }

            /*
             * Make sure Carbon instance
             */
            $expiry = $expiry instanceof Carbon
                ? $expiry
                : Carbon::parse($expiry);

            /*
             * ---------------------------------------------------------
             * Get registrar
             * ---------------------------------------------------------
             *
             * Spatie DomainResponse exposes the complete RDAP
             * response through all().
             */
            $registrar = $this->getRegistrar(
                $domainResponse->all()
            );

            /*
             * ---------------------------------------------------------
             * Calculate remaining days
             * ---------------------------------------------------------
             */
            $daysRemaining = (int) ceil(
                now()->diffInDays(
                    $expiry,
                    false
                )
            );

            /*
             * ---------------------------------------------------------
             * Determine domain status
             * ---------------------------------------------------------
             */
            $domainStatus = match (true) {
                $daysRemaining < 0 => 'expired',
                $daysRemaining <= 30 => 'warning',
                default => 'active',
            };

            Log::info('Domain expiry calculated.', [
                'monitor_id' => $monitor->id,
                'domain' => $domain,
                'expiry_date' => $expiry->toDateTimeString(),
                'days_remaining' => $daysRemaining,
                'status' => $domainStatus,
                'registrar' => $registrar,
            ]);

            /*
             * ---------------------------------------------------------
             * Save domain check result
             * ---------------------------------------------------------
             */
            $checkResult = $monitor
                ->checkResult()
                ->firstOrCreate([]);

            $checkResult->update([
                'domain_expires_at' => $expiry,
                'domain_days_remaining' => $daysRemaining,
                'domain_registrar' => $registrar,
                'domain_status' => $domainStatus,
                'domain_checked_at' => now(),
            ]);

            /*
             * ---------------------------------------------------------
             * Expired domain = DOWN
             * ---------------------------------------------------------
             */
            if ($domainStatus === 'expired') {
                $monitor->update([
                    'status' => 'down',
                ]);

                MonitorLog::create([
                    'monitor_id' => $monitor->id,
                    'status' => 'down',
                    'response_time' => null,
                    'status_code' => null,
                    'message' => 'Domain expired.',
                    'checked_at' => now(),
                ]);

                Log::warning(
                    'Monitor marked DOWN because domain expired.',
                    [
                        'monitor_id' => $monitor->id,
                        'domain' => $domain,
                        'expiry_date' => $expiry->toDateTimeString(),
                    ]
                );
            }

            /*
             * ---------------------------------------------------------
             * Warning / Active
             * ---------------------------------------------------------
             *
             * Do NOT mark monitor UP here.
             *
             * Uptime job will decide the final UP/DOWN status.
             */
            Log::info('Domain expiry check completed.', [
                'monitor_id' => $monitor->id,
                'domain' => $domain,
                'status' => $domainStatus,
            ]);
        } catch (Throwable $e) {
            Log::error('Domain expiry check failed.', [
                'monitor_id' => $this->monitorId,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            /*
             * Re-throw so Laravel queue marks the job as failed.
             */
            throw $e;
        }
    }

    /**
     * Get registrable/root domain.
     *
     * Examples:
     *
     * app.example.com
     *      ↓
     * example.com
     *
     * app.example.co.in
     *      ↓
     * example.co.in
     */
    private function getRegistrableDomain(
        string $host
    ): ?string {
        $host = strtolower(
            trim($host, '.')
        );

        $parts = explode('.', $host);

        if (count($parts) < 2) {
            return null;
        }

        /*
         * Common second-level TLDs.
         */
        $secondLevelTlds = [
            'co.uk',
            'org.uk',
            'me.uk',
            'ac.uk',
            'gov.uk',

            'co.in',
            'com.in',
            'net.in',
            'org.in',
            'firm.in',
            'gen.in',
            'ind.in',

            'com.au',
            'co.au',
            'net.au',
            'org.au',

            'co.nz',
            'net.nz',
            'org.nz',

            'co.jp',
            'ne.jp',
            'or.jp',

            'com.br',
            'net.br',
            'org.br',

            'co.za',
            'org.za',
            'net.za',
        ];

        $lastTwo = implode(
            '.',
            array_slice($parts, -2)
        );

        /*
         * Example:
         *
         * example.co.in
         *       ↓
         * example.co.in
         */
        if (in_array(
            $lastTwo,
            $secondLevelTlds,
            true
        )) {
            if (count($parts) < 3) {
                return null;
            }

            return implode(
                '.',
                array_slice($parts, -3)
            );
        }

        /*
         * Example:
         *
         * example.com
         *       ↓
         * example.com
         */
        return $lastTwo;
    }

    /**
     * Extract registrar from RDAP response.
     */
    private function getRegistrar(
        array $rdap
    ): ?string {
        foreach (
            $rdap['entities'] ?? [] as $entity
        ) {
            $roles = array_map(
                'strtolower',
                $entity['roles'] ?? []
            );

            if (!in_array(
                'registrar',
                $roles,
                true
            )) {
                continue;
            }

            /*
             * vCard format
             */
            foreach (
                $entity['vcardArray'][1] ?? [] as $vcard
            ) {
                if (
                    isset($vcard[0]) &&
                    strtolower($vcard[0]) === 'fn' &&
                    isset($vcard[3])
                ) {
                    return is_array($vcard[3])
                        ? ($vcard[3][0] ?? null)
                        : $vcard[3];
                }
            }

            /*
             * Fallback
             */
            if (!empty($entity['fn'])) {
                return $entity['fn'];
            }
        }

        return null;
    }

    /**
     * Save UNKNOWN result.
     */
    private function saveUnknownResult(
        Monitor $monitor,
        string $reason,
        ?string $registrar = null
    ): void {
        Log::warning(
            'Saving domain result as UNKNOWN.',
            [
                'monitor_id' => $monitor->id,
                'url' => $monitor->url,
                'reason' => $reason,
            ]
        );

        $checkResult = $monitor
            ->checkResult()
            ->firstOrCreate([]);

        $checkResult->update([
            'domain_expires_at' => null,
            'domain_days_remaining' => null,
            'domain_registrar' => $registrar,
            'domain_status' => 'unknown',
            'domain_checked_at' => now(),
        ]);
    }
}
