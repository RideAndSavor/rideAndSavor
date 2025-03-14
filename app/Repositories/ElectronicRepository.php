<?php

namespace App\Repositories;

use App\Models\Electronic;
use App\Repositories\BaseRepository;
use App\Contracts\ElectronicInterface;

class ElectronicRepository extends BaseRepository implements ElectronicInterface
{
    public function __construct()
    {
        parent::__construct(class_basename(Electronic::class));
    }

}
