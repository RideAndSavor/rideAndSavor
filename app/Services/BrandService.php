<?php
namespace App\Services;

use App\Contracts\BrandInterface;
use App\Models\Brand;

class BrandService
{
    protected $repository;
    public function __construct(BrandInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllBrands()
    {
        return $this->repository->all();
    }


    public function store(array $data): Brand
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
        return $this->repository->getById($id);
    }

}
