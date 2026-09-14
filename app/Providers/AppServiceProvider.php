<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Services\MailConfigService;
use Illuminate\Support\Facades\Schema;


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
       // Settings table may not exist during migrations.
        if (!Schema::hasTable('settings')) {
            return;
        }

        // Load mail configuration from settings table
        $mailConfigService->apply();
    }
}
