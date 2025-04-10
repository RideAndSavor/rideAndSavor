<?php
namespace App\Services;

use App\Contracts\BrandInterface;
use App\Contracts\ShopInterface;
use App\Models\Brand;
use App\Models\Shop;

class ShopService
{
    protected $repository;
    public function __construct(ShopInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllShops()
    {
        // return $this->repository->all('address.street.ward.township.city');
        return $this->repository->all(['address', 'images']);
    }


    public function store(array $data): Shop
    {
        // dd($data);
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
