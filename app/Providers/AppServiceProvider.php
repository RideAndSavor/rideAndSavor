<?php

namespace App\Providers;

use App\Contracts\AcceptDriverInterface;
use App\Contracts\BaseInterface;
use App\Contracts\BiddingPriceByDriverInterface;
use App\Contracts\BrandInterface;
use App\Contracts\CityInterface;
use App\Contracts\WardInterface;
use App\Contracts\StateInterface;
use App\Contracts\StreetInterface;
use App\Contracts\CountryInterface;
use App\Contracts\ElectronicInterface;
use App\Contracts\ImageInterface;
use App\Contracts\InventoryInterface;
use App\Contracts\InvoiceInterface;
use App\Contracts\NearbyTaxiInterface;
use App\Contracts\ProductInterface;
use App\Contracts\ShopInterface;
use App\Contracts\TaxiDriverInterface as ContractsTaxiDriverInterface;
use App\Contracts\TownshipInterface;
use App\Contracts\TravelInterface;
use App\Repositories\AcceptDriverRepository;
use App\Repositories\BaseRepository;
use App\Repositories\biddingPriceByDriverRepository;
use App\Repositories\BrandRepository;
use App\Repositories\CityRepository;
use App\Repositories\ShopRepository;
use App\Repositories\WardRepository;
use App\Repositories\StateRepository;
use App\Repositories\StreetRepository;
use App\Repositories\CountryRepository;
use App\Repositories\ElectronicRepository;
use App\Repositories\ImageRepository;
use App\Repositories\Interfaces\TaxiDriverInterface;
use App\Repositories\InventoryRepository;
use App\Repositories\InvoiceRepository;
use App\Repositories\NearbyTaxiRepository;
use App\Repositories\ProductRepository;
use App\Repositories\TaxiDriverRepository;
use Illuminate\Support\ServiceProvider;
use App\Repositories\TownshipRepository;
use App\Repositories\TravelRepository;
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
        $this->app->bind(ImageInterface::class, ImageRepository::class);
        $this->app->bind(ContractsTaxiDriverInterface::class, TaxiDriverRepository::class);
        $this->app->bind(TravelInterface::class, TravelRepository::class);
        $this->app->bind(BiddingPriceByDriverInterface::class, BiddingPriceByDriverRepository::class);
        $this->app->bind(AcceptDriverInterface::class, AcceptDriverRepository::class);
        $this->app->bind(NearbyTaxiInterface::class, NearbyTaxiRepository::class);
        $this->app->bind(InvoiceInterface::class, InvoiceRepository::class);

        $this->app->bind(ElectronicInterface::class, ElectronicRepository::class);


        $this->app->bind(InventoryInterface::class, InventoryRepository::class);

        $this->app->bind(BrandInterface::class, BrandRepository::class);
        $this->app->bind(ShopInterface::class, ShopRepository::class);
        $this->app->bind(ProductInterface::class, ProductRepository::class);



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
