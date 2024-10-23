<?php

namespace App\Contracts;

interface CityInterface
{
    public function getCitiesByState($state_id);
}
