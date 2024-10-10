<?php

namespace App\Contracts;

interface StateInterface
{
    public function getStatesByCountry($country_id);
}
