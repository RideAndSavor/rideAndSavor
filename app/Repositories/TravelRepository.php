<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Travel;
use App\Models\TaxiDriver;
use App\Contracts\TravelInterface;

class TravelRepository extends BaseRepository implements TravelInterface
{
    public function __construct()
    {
        parent::__construct(class_basename("Travel"));
    }

    public function updateStatus(int $travelId, string $status)
    {
        $travel = Travel::find($travelId);
        if ($travel) {
            $travel->status = $status; // Set the status to 'accepted' or whatever the passed status is
            $travel->save();
            return $travel;
        }
        return null;
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
