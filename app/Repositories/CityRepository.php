<?php
namespace App\Repositories;

use App\Contracts\CityInterface;
use App\Models\City;
use App\Models\State;

class CityRepository implements CityInterface
{
    public function getCitiesByState($state_id)
    {
        return City::where('state_id', $state_id)->get();
    }
}
