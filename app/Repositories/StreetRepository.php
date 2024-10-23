<?php
namespace App\Repositories;

use App\Contracts\StreetInterface;
use App\Models\Street;

class StreetRepository implements StreetInterface
{
    public function getStreetsByWard($ward_id)
    {
        return Street::where('ward_id', $ward_id)->get();
    }
    
}
