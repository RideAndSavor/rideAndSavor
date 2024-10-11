<?php

namespace App\Contracts;

interface StreetInterface
{
    public function getStreetsByWard($ward_id);
}
