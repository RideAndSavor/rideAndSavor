<?php
namespace App\Repositories;

use App\Models\Country;
use App\Contracts\CountryInterface;

class CountryRepository implements CountryInterface
{
    public function getAllCountries()
    {
        return Country::all();
    }
}
