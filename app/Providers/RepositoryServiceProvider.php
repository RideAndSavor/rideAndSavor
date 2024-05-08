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
            'App\Contracts\CountryInterface',
            'App\Repositories\CountryRepository'

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
