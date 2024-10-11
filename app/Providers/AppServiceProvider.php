<?php

namespace App\Providers;

use App\Contracts\CityInterface;
use App\Contracts\StateInterface;
<<<<<<< HEAD
use App\Contracts\CountryInterface;
=======
use App\Contracts\StreetInterface;
use App\Contracts\TownshipInterface;
use App\Contracts\WardInterface;
>>>>>>> f90a6dfc91f8f0d3e639d0978af989ec9d332469
use App\Repositories\CityRepository;
use App\Repositories\StateRepository;
<<<<<<< HEAD
use App\Repositories\CountryRepository;
=======
use App\Repositories\StreetRepository;
use App\Repositories\TownshipRepository;
use App\Repositories\WardRepository;
>>>>>>> f90a6dfc91f8f0d3e639d0978af989ec9d332469
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
        $this->app->bind(TownshipInterface::class, TownshipRepository::class);
        $this->app->bind(WardInterface::class, WardRepository::class);
        $this->app->bind(StreetInterface::class, StreetRepository::class);
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
