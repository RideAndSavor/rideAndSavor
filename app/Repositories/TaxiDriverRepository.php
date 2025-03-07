<?php

namespace App\Repositories;

use App\Models\NearbyTaxi;
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

    public function getPendingRidesForDriver($driverId)
    {
        return NearbyTaxi::with('travel', 'taxiDriver')
            ->where('taxi_driver_id', $driverId)
            ->whereHas('travel', function ($query) {
                $query->where('status', 'pending');
            })
            ->get();
    }
}
