<?php

namespace App\Contracts;

interface TripInterface
{
    public function findwhere($modleName, $user_id);
    public function findUser($modleName, $user_id);
}
