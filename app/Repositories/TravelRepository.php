<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\Travel;
use App\Models\TaxiDriver;
use App\Contracts\TravelInterface;

class TravelRepository extends BaseRepository implements TravelInterface
{
    public function __construct()
    {
        parent::__construct(class_basename("Travel"));
    }

    public function updateStatus(int $travelId, string $status)
    {
        $travel = Travel::find($travelId);
        if ($travel) {
            $travel->status = $status; // Set the status to 'accepted' or whatever the passed status is
            $travel->save();
            return $travel;
        }
        return null;
    }

}

