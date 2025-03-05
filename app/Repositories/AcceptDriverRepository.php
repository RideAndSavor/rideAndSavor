<?php

namespace App\Repositories;

use App\Models\Travel;
use App\Models\AcceptDriver;
use App\Models\BiddingPriceByDriver;
use App\Contracts\AcceptDriverInterface;

class AcceptDriverRepository extends BaseRepository implements AcceptDriverInterface
{
    public function __construct()
    {
        parent::__construct(class_basename(AcceptDriver::class));
    }
}
