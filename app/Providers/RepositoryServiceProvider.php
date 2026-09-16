<?php

namespace App\Providers;

use App\Repositories\Contracts\{
    ActivityLogRepositoryInterface,
    UserRepositoryInterface,
    SettingRepositoryInterface,
    MonitorRepositoryInterface,
    DashboardRepositoryInterface,
    MonitorLogRepositoryInterface,
    EmailLogRepositoryInterface
};

use App\Repositories\{
    ActivityLogRepository,
    UserRepository,
    SettingRepository,
    MonitorRepository,
    DashboardRepository,
    MonitorLogRepository,
    EmailLogRepository
};

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     * 
     * @return void
     * 
     */
    public function register(): void
    {
        // User Repository Binding
        $this->app->bind(
            UserRepositoryInterface::class,
            UserRepository::class
        );

        /* Setting Repository Binding */
        $this->app->bind(
            SettingRepositoryInterface::class,
            SettingRepository::class
        );

        /* Monitor Repository Binding */
        $this->app->bind(
            MonitorRepositoryInterface::class,
            MonitorRepository::class
        );

        /* Dashboard Repository Binding */
        $this->app->bind(
            DashboardRepositoryInterface::class,
            DashboardRepository::class
        );
        // Activity Repository Binding
        $this->app->bind(
            ActivityLogRepositoryInterface::class,
            ActivityLogRepository::class
        );

        // Monitor Log Repository Binding
        $this->app->bind(
            MonitorLogRepositoryInterface::class,
            MonitorLogRepository::class
        );

        // Email Log Repository Binding
        $this->app->bind(
            EmailLogRepositoryInterface::class,
            EmailLogRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
