<?php

namespace App\Contracts;

interface TravelInterface extends BaseInterface
{
    public function getNearbyDrivers(float $latitude, float $longitude, int $radius);
    public function updateStatus(int $travelId, string $status);
}

