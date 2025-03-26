<?php

namespace App\Contracts;

interface BiddingPriceByDriverInterface extends BaseInterface
{
    // public function updateStatus(int $travelId, string $status);
    public function deleteByTravelId(int $travelId);

    public function getBiddingPricesByTravelId(int $travelId);

}
