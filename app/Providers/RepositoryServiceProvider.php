<?php

namespace App\Providers;

use App\Repositories\Contracts\{
    UserRepositoryInterface,
    SettingRepositoryInterface
};

use App\Repositories\{
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
    }

    public function boot(): void
    {
        //
    }
}