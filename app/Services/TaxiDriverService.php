<?php
namespace App\Services;

use App\Repositories\TaxiDriverRepository;

class TaxiDriverService
{
    protected $taxiDriverRepository;

    public function __construct(TaxiDriverRepository $taxiDriverRepository)
    {
        $this->taxiDriverRepository = $taxiDriverRepository;
    }

    public function getNearbyDrivers($latitude, $longitude, $radius)
    {
        // dd($latitude, $longitude);
        return $this->taxiDriverRepository->getNearbyDrivers($latitude, $longitude, $radius);
    }
}

