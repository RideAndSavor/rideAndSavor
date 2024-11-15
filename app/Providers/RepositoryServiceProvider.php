<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //Bind the contracts and repositories
        $this->app->bind(
            'App\Contracts\LocationInterface',
            'App\Repositories\LocationRepository'
        );
        $this->app->bind(
            'App\Contracts\UserInterface',
            'App\Repositories\UserRepository'
        );
        $this->app->bind(
            'App\Contracts\PaymentProviderInterface',
            'App\Repositories\PaymentProviderRepository'
        );
        $this->app->bind(
            'App\Contracts\TripInterface',
            'App\Repositories\TripRepository'
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
