<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Services\MailConfigService;

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
    }
}
 