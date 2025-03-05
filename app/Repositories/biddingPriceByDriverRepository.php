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
        // Delete the bidding price entry where the travel_id matches
        return BiddingPriceByDriver::where('travel_id', $travelId)->delete();
    }

}
