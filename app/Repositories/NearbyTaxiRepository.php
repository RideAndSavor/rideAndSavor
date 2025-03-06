<?php

namespace App\Repositories;

use App\Contracts\NearbyTaxiInterface;
use App\Models\NearbyTaxi;

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
}
