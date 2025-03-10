<?php

namespace App\Repositories;

use App\Models\NearbyTaxi;
use App\Models\TaxiDriver;
use App\Contracts\NearbyTaxiInterface;

class NearbyTaxiRepository extends BaseRepository implements NearbyTaxiInterface
{
    public function __construct()
    {
        // dd("ok");
        parent::__construct(class_basename(NearbyTaxi::class));
    }
    public function deleteByTravelId(int $travelId)
    {
        return NearbyTaxi::where('travel_id', $travelId)->delete();
    }

    public function getNearbyDrivers($latitude, $longitude, $radius = 1)
    {
        return TaxiDriver::selectRaw("
                *, (6371 * acos(
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                )) AS distance",
                [$latitude, $longitude, $latitude]
            )
            ->having("distance", "<=", 1)
            ->orderBy("distance", "asc")
            ->get();
    }

    public function storeNearbyDrivers($travelId, $nearbyDrivers)
    {
        foreach ($nearbyDrivers as $driver) {
            NearByTaxi::updateOrCreate(
                [
                    'travel_id' => $travelId,
                    'taxi_driver_id' => $driver->id,
                ],
                [
                    'driver_name' => $driver->user->name,
                    'plate_number' => $driver->license_plate,
                ]
            );
        }
    }

}
