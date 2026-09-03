<?php

namespace App\Providers;

use App\Repositories\Contracts\{
    UserRepositoryInterface,
    SettingRepositoryInterface,
    MonitorRepositoryInterface,
    DashboardRepositoryInterface
};

use App\Repositories\{
    UserRepository,
    SettingRepository,
    MonitorRepository,
    DashboardRepository
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
    }

    public function boot(): void
    {
        //
    }
}