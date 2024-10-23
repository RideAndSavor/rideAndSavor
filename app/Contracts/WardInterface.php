<?php

namespace App\Contracts;

interface WardInterface
{
    public function getWardsByTownship($township_id);
}
