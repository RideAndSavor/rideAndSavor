<?php

namespace App\Contracts;

use App\Contracts\BaseInterface;

interface TaxiDriverInterface extends BaseInterface
{
    public function getNearbyDrivers($latitude, $longitude, $radius = 1);
}
