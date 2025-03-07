<?php

namespace App\Contracts;

use App\Contracts\BaseInterface;

interface TaxiDriverInterface extends BaseInterface
{
    public function getPendingRidesForDriver($driverId);
}
