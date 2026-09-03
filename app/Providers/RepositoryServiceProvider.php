<?php

namespace App\Providers;

use App\Repositories\Contracts\{
    ActivityLogRepositoryInterface,
    UserRepositoryInterface,
    SettingRepositoryInterface
};

use App\Repositories\{
    ActivityLogRepository,
    UserRepository,
    SettingRepository
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

        // Setting Repository Binding
        $this->app->bind(
            SettingRepositoryInterface::class,
            SettingRepository::class
        );

        // Activity Repository Binding
        $this->app->bind(
            ActivityLogRepositoryInterface::class,
            ActivityLogRepository::class
        );
    }

    public function boot(): void
    {
        //
    }
}
