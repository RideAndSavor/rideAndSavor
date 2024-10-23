<?php
namespace App\Repositories;

use App\Contracts\WardInterface;
use App\Models\Ward;

class WardRepository implements WardInterface
{
    public function getWardsByTownship($township_id)
    {
        return Ward::where('township_id', $township_id)->get();
    }
}