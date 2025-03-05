<?php

namespace App\Repositories;

use App\Models\TaxiDriver;
use Illuminate\Support\Facades\DB;
use App\Contracts\TaxiDriverInterface;

class TaxiDriverRepository extends BaseRepository implements TaxiDriverInterface
{
    public function __construct()
    {
        // Optional: If you're using a BaseRepository, make sure the class name is passed correctly.
        parent::__construct(class_basename(TaxiDriver::class)); // Pass the class name correctly
    }

    public function getNearbyDrivers($latitude, $longitude, $radius = 1)
    {
        // Earth's radius in kilometers
        $earthRadius = 6371;
        // dd($latitude, $longitude);
        return TaxiDriver::selectRaw("
            taxi_drivers.*,
            ($earthRadius * acos(
                cos(radians(?))
                * cos(radians(taxi_drivers.latitude))
                * cos(radians(taxi_drivers.longitude) - radians(?))
                + sin(radians(?))
                * sin(radians(taxi_drivers.latitude))
            )) AS distance", [$latitude, $longitude, $latitude])
            ->where('is_available', 1) // Only available drivers
            ->having('distance', '<=', $radius) // Filter by radius
            ->orderBy('distance', 'asc') // Order by distance, closest first
            ->get();
    }



}
