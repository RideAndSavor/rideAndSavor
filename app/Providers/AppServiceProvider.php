<?php

namespace App\Providers;

use App\Contracts\CityInterface;
use App\Contracts\StateInterface;
use App\Contracts\CountryInterface;
use App\Repositories\CityRepository;
use App\Repositories\StateRepository;
use App\Repositories\CountryRepository;
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
        $this->app->bind(CityInterface::class, CityRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        VerifyCsrfToken::except([
            'api/*', // Exclude your API route
        ]);
    }
}
