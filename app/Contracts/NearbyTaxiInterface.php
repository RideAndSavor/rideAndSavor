<?php

namespace App\Contracts;

interface NearbyTaxiInterface extends BaseInterface
{
    public function deleteByTravelId(int $travelId);
    public function getNearbyDrivers($latitude, $longitude, $radius);
    public function storeNearbyDrivers($travelId, $nearbyDrivers);
}
