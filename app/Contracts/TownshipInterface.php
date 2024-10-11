<?php

namespace App\Contracts;

interface TownshipInterface
{
    public function getTownshipsByCity($city_id);
}
