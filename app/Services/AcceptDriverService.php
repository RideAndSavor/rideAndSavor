<?php

namespace App\Services;

use App\Contracts\AcceptDriverInterface;
use App\Models\AcceptDriver;

class AcceptDriverService
{
    protected $repository;

    public function __construct(AcceptDriverInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllAcceptedDrivers()
    {
        return $this->repository->all();
    }

    public function store(array $data): AcceptDriver
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

}
