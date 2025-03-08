<?php

namespace App\Contracts;

interface TravelInterface extends BaseInterface
{

    public function updateStatus(int $travelId, string $status);
}

