<?php
namespace App\Repositories;

use App\Contracts\ProductInterface;

class ProductRepository extends BaseRepository implements  ProductInterface
{
    public function __construct()
    {
        parent::__construct(class_basename("Product"));
    }

}
