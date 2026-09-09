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
        // Load mail configuration from settings table
        $mailConfigService->apply();
    }
}
