<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Services\MailConfigService;
use Illuminate\Support\Facades\Http;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(
        MailConfigService $mailConfigService
    ): void
    {
        Paginator::useBootstrapFive();

        /*
        |--------------------------------------------------------------------------
        | Apply mail configuration from settings
        |--------------------------------------------------------------------------
        |
        | Do not query the settings table while running Artisan commands.
        | During commands like migrate:fresh, migrations may not have
        | created the settings table yet.
        |
        */
        
        if (! app()->runningInConsole()) {
            $mailConfigService->apply();
        }
        

        /*
        |--------------------------------------------------------------------------
        | Global SSL certificate
        |--------------------------------------------------------------------------
        |
        | Apply a global SSL certificate to all HTTP requests.
        |
        */
        $caBundle = storage_path('certs/cacert.pem');

        if (is_file($caBundle)) {
            Http::globalOptions([
                'verify' => $caBundle,
            ]);
        }
    }
}
 