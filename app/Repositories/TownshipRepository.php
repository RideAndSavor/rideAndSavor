<?php
namespace App\Repositories;

use App\Contracts\TownshipInterface;
use App\Models\Township;

class TownshipRepository implements TownshipInterface
{
    public function getTownshipsByCity($city_id)
    {
        return Township::where('city_id', $city_id)->get();
    }
}