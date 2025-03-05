<?php

namespace App\Repositories;

use App\Contracts\TravelInterface;
use App\Models\Travel;
use App\Models\User;

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

    public function findNearbyDrivers($latitude, $longitude, $radius)
    {
        return User::selectRaw("id,
                (6371 * acos(cos(radians(?)) * cos(radians(latitude))
                * cos(radians(longitude) - radians(?)) + sin(radians(?))
                * sin(radians(latitude)))) AS distance", [$latitude, $longitude, $latitude])
            ->having("distance", "<", $radius)
            ->orderBy("distance", "asc")
            ->get();
    }
}
