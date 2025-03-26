<?php

namespace App\Repositories;

use App\Models\Travel;
use App\Models\BiddingPriceByDriver;
use App\Contracts\BiddingPriceByDriverInterface;

class BiddingPriceByDriverRepository extends BaseRepository implements BiddingPriceByDriverInterface
{
    public function __construct()
    {
        parent::__construct(class_basename(BiddingPriceByDriver::class));
    }

    public function deleteByTravelId(int $travelId)
    {
        return BiddingPriceByDriver::where('travel_id', $travelId)->delete();
    }

    public function getBiddingPricesByTravelId($travelId)
{
    return BiddingPriceByDriver::where('travel_id', $travelId)
        ->with('driver') // Assuming there's a `driver` relationship in BiddingPrice model
        ->get();
}

}
