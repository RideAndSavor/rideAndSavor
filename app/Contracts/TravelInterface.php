<?php

namespace App\Contracts;

interface TravelInterface extends BaseInterface
{
    public function findNearbyDrivers(float $latitude, float $longitude, int $radius);
    public function updateStatus(int $travelId, string $status);
}

