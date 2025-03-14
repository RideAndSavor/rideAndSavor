<?php
namespace App\Repositories;

use App\Contracts\BrandInterface;


class PricingRepository extends BaseRepository implements  BrandInterface
{
    public function __construct()
    {
        parent::__construct(class_basename("Brand"));
    }

}
