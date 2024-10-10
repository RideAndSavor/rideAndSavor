<?php

namespace App\Providers;

use App\Contracts\CountryInterface;
use App\Contracts\StateInterface;
use App\Repositories\CountryRepository;
use App\Repositories\StateRepository;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->register(RepositoryServiceProvider::class);
        $this->app->bind(CountryInterface::class, CountryRepository::class);
        $this->app->bind(StateInterface::class, StateRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // VerifyCsrfToken::except([
        //     'api/*', // Exclude your API route
        // ]);
    }
}
