<?php
namespace App\Repositories;

use App\Models\Township;

class TownshipRepository
{
    public function getTownshipsByCity($city_id)
    {
        return Township::where('city_id', $city_id)->get();
    }
}