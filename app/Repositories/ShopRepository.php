<?php
namespace App\Repositories;
use App\Contracts\ShopInterface;

class ShopRepository extends BaseRepository implements  ShopInterface
{
    public function __construct()
    {
        parent::__construct(class_basename("Shop"));
    }

}
