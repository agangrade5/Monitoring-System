<?php

namespace App\Providers;

use App\Repositories\Contracts\{
    UserRepositoryInterface,
    SettingRepositoryInterface,
    MonitorRepositoryInterface
};

use App\Repositories\{
    UserRepository,
    SettingRepository,
    MonitorRepository
};

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
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
    }

    public function boot(): void
    {
        //
    }
}