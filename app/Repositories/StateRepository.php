<?php
namespace App\Repositories;

use App\Models\State;
use App\Contracts\StateInterface;

class StateRepository implements StateInterface
{
    public function getStatesByCountry($country_id)
    {
        return State::where('country_id', $country_id)->get();
    }
}
