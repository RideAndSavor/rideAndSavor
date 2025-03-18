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
        // dd($travelId);
        // Delete the bidding price entry where the travel_id matches
        return BiddingPriceByDriver::where('travel_id', $travelId)->delete();
    }

    public function getBiddingPricesByUserId($userId)
{
    return BiddingPriceByDriver::whereHas('travel', function ($query) use ($userId) {
        $query->where('user_id', $userId);
    })
    ->with('driver') // Assuming there's a `driver` relationship in BiddingPrice model
    ->get();
}
}
