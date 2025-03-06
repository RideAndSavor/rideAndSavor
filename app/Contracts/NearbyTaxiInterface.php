<?php

namespace App\Contracts;

interface NearbyTaxiInterface extends BaseInterface
{
    public function deleteByTravelId(int $travelId);
}
