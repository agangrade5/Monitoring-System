<?php

namespace App\Jobs;

use App\Models\Monitor;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class CheckPhpVersionJob implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public int $monitorId
    ) {}

    public function handle(): void
    {
        $monitor = Monitor::with('settings')->find($this->monitorId);

        if (
            !$monitor ||
            !$monitor->is_active ||
            !$monitor->url
        ) {
            return;
        }

        if (
            $monitor->settings &&
            !$monitor->settings->check_php
        ) {
            return;
        }

        try {

            $response = Http::timeout(10)
                ->withoutVerifying()
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0',
                    'Accept' => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
                ])
                ->get($monitor->url);
             \Log::info('PHP VERSION DEBUG', [ 'url' => $monitor->url, 'status' => $response->status(), 'server' => $response->header('Server'), 'powered_by' => $response->header('X-Powered-By'), 'headers' => $response->headers(), ]);
            $html = $response->body();

            /*
             * Detect PHP version
             */
            $phpVersion = $this->extractPhpVersion(
                $response,
                $html
            );

            /*
             * Detect WordPress
             */
            $wpVersion = $this->extractWordPressVersion(
                $html
            );

            /*
             * Detect CodeIgniter
             */
            $codeIgniterVersion = $this->extractCodeIgniterVersion(
                $response,
                $html
            );

            /*
             * Detect Laravel
             */
            $laravelVersion = $this->extractLaravelVersion(
                $response,
                $html
            );

            /*
             * Detect framework
             */
            $framework = $this->detectFramework(
                $response,
                $html,
                $wpVersion,
                $codeIgniterVersion,
                $laravelVersion
            );

            $monitor->checkResult()->updateOrCreate(
                [
                    'monitor_id' => $monitor->id,
                ],
                [
                    'php_version' => $phpVersion ?: 'Unknown',

                    'php_status' => $phpVersion
                        ? 'up'
                        : 'unknown',

                    'php_checked_at' => now(),

                    'wordpress_version' =>
                        $wpVersion ?: 'Unknown',

                    'codeigniter_version' =>
                        $codeIgniterVersion ?: 'Unknown',

                    'laravel_version' =>
                        $laravelVersion ?: 'Unknown',

                    'framework' =>
                        $framework ?: 'Unknown',
                ]
            );

        } catch (\Throwable $e) {

            report($e);

            $monitor->checkResult()->updateOrCreate(
                [
                    'monitor_id' => $monitor->id,
                ],
                [
                    'php_version' => 'Unknown',
                    'php_status' => 'unknown',
                    'php_checked_at' => now(),

                    'wordpress_version' => 'Unknown',
                    'codeigniter_version' => 'Unknown',
                    'laravel_version' => 'Unknown',
                    'framework' => 'Unknown',
                ]
            );
        }
    }

    /**
     * Detect PHP version.
     */
    private function extractPhpVersion(
        Response $response,
        string $html
    ): ?string {

        /*
         * ---------------------------------------------------------
         * 1. X-Powered-By
         * ---------------------------------------------------------
         *
         * Example:
         * PHP/8.2.12
         */
        $poweredBy = $response->header('X-Powered-By');

        if ($poweredBy) {

            if (preg_match(
                '/PHP\/([0-9]+(?:\.[0-9]+){1,2})/i',
                $poweredBy,
                $matches
            )) {
                return $matches[1];
            }
        }

        /*
         * ---------------------------------------------------------
         * 2. Server header
         * ---------------------------------------------------------
         */
        $server = $response->header('Server');

        if ($server) {

            /*
             * PHP/8.2.12
             */
            if (preg_match(
                '/PHP\/([0-9]+(?:\.[0-9]+){1,2})/i',
                $server,
                $matches
            )) {
                return $matches[1];
            }

            /*
             * PHP 8.2.12
             */
            if (preg_match(
                '/PHP\s+([0-9]+(?:\.[0-9]+){1,2})/i',
                $server,
                $matches
            )) {
                return $matches[1];
            }
        }

        /*
         * ---------------------------------------------------------
         * 3. Complete response headers
         * ---------------------------------------------------------
         */
        foreach ($response->headers() as $name => $values) {

            $value = is_array($values)
                ? implode(' ', $values)
                : $values;

            if (preg_match(
                '/PHP\/([0-9]+(?:\.[0-9]+){1,2})/i',
                $value,
                $matches
            )) {
                return $matches[1];
            }

            if (preg_match(
                '/PHP\s+([0-9]+(?:\.[0-9]+){1,2})/i',
                $value,
                $matches
            )) {
                return $matches[1];
            }
        }

        /*
         * ---------------------------------------------------------
         * 4. HTML
         * ---------------------------------------------------------
         *
         * Only return version when PHP version is explicitly
         * present in public HTML.
         */
        $patterns = [
            '/PHP\/([0-9]+(?:\.[0-9]+){1,2})/i',

            '/PHP\s+version\s*[:=]\s*([0-9]+(?:\.[0-9]+){1,2})/i',

            '/PHP\s+([0-9]+(?:\.[0-9]+){1,2})/i',
        ];

        foreach ($patterns as $pattern) {

            if (preg_match(
                $pattern,
                $html,
                $matches
            )) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Detect WordPress version.
     */
    private function extractWordPressVersion(
        string $html
    ): ?string {

        $patterns = [

            '/<meta[^>]+name=["\']generator["\'][^>]+content=["\']WordPress\s+([0-9.]+)["\']/i',

            '/<meta[^>]+content=["\']WordPress\s+([0-9.]+)["\'][^>]+name=["\']generator["\']/i',

            '/<generator[^>]*>https?:\/\/wordpress\.org\/\?v=([0-9.]+)<\/generator>/i',

        ];

        foreach ($patterns as $pattern) {

            if (preg_match(
                $pattern,
                $html,
                $matches
            )) {
                return $matches[1];
            }
        }

        /*
         * WordPress asset version.
         */
        if (preg_match(
            '/(?:wp-includes|wp-content)[^"\']*[\?&]ver=([0-9]+\.[0-9]+(?:\.[0-9]+)?)/i',
            $html,
            $matches
        )) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Detect CodeIgniter version.
     *
     * Note:
     * CodeIgniter normally does NOT expose its version publicly.
     */
    private function extractCodeIgniterVersion(
        Response $response,
        string $html
    ): ?string {

        $content = $html;

        foreach ($response->headers() as $name => $values) {

            $content .= ' ' . $name . ' ';

            $content .= is_array($values)
                ? implode(' ', $values)
                : $values;
        }

        $patterns = [

            '/CodeIgniter\s+([0-9]+(?:\.[0-9]+){1,2})/i',

            '/CodeIgniter\/([0-9]+(?:\.[0-9]+){1,2})/i',

            '/CodeIgniter\s+Framework\s+([0-9]+(?:\.[0-9]+){1,2})/i',

        ];

        foreach ($patterns as $pattern) {

            if (preg_match(
                $pattern,
                $content,
                $matches
            )) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Detect Laravel version.
     */
    private function extractLaravelVersion(
        Response $response,
        string $html
    ): ?string {

        $content = $html;

        foreach ($response->headers() as $name => $values) {

            $content .= ' ' . $name . ' ';

            $content .= is_array($values)
                ? implode(' ', $values)
                : $values;
        }

        $patterns = [

            '/Laravel\s+Framework\s+([0-9]+(?:\.[0-9]+){1,2})/i',

            '/Laravel\s+v([0-9]+(?:\.[0-9]+){1,2})/i',

            '/Laravel\/([0-9]+(?:\.[0-9]+){1,2})/i',

        ];

        foreach ($patterns as $pattern) {

            if (preg_match(
                $pattern,
                $content,
                $matches
            )) {
                return $matches[1];
            }
        }

        return null;
    }

    /**
     * Detect framework.
     */
    private function detectFramework(
        Response $response,
        string $html,
        ?string $wpVersion,
        ?string $codeIgniterVersion,
        ?string $laravelVersion
    ): ?string {

        /*
         * WordPress
         */
        if ($wpVersion) {
            return 'WordPress';
        }

        if (
            str_contains(
                strtolower($html),
                'wp-content'
            ) ||
            str_contains(
                strtolower($html),
                'wp-includes'
            )
        ) {
            return 'WordPress';
        }

        /*
         * CodeIgniter
         */
        if ($codeIgniterVersion) {
            return 'CodeIgniter';
        }

        if (
            str_contains(
                strtolower($html),
                'codeigniter'
            )
        ) {
            return 'CodeIgniter';
        }

        /*
         * Laravel
         */
        if ($laravelVersion) {
            return 'Laravel';
        }

        $cookies = strtolower(
            $response->header(
                'Set-Cookie',
                ''
            )
        );

        if (
            str_contains(
                $cookies,
                'laravel_session'
            )
        ) {
            return 'Laravel';
        }

        /*
         * Generic PHP
         */
        if (
            $this->extractPhpVersion(
                $response,
                $html
            )
        ) {
            return 'PHP';
        }

        return null;
    }
}

