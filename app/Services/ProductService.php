<?php
namespace App\Services;

use App\Models\Product;
use App\Contracts\ProductInterface;

class ProductService
{
    protected $repository;
    public function __construct(ProductInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllProducts()
    {
        return $this->repository->all(['images', 'shop', 'subcategory', 'brand']);
    }


    public function store(array $data): Product
    {
        return $this->repository->store($data);
    }

    public function update(array $data, int $id)
    {
        return $this->repository->update($data, $id);
    }

    public function delete(int $id)
    {
        $this->repository->delete($id);
    }

    public function getById(int $id)
    {
        return $this->repository->getById($id, ['images', 'shop', 'subcategory', 'brand']);
    }


}
