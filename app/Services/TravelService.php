<?php

namespace App\Services;

use App\Contracts\TravelInterface;
use App\Models\Travel;

class TravelService
{
    protected $repository;

    public function __construct(TravelInterface $repository)
    {
        $this->repository = $repository;
    }


    public function updateStatus(int $travelId, string $status)
    {
        return $this->repository->updateStatus($travelId, $status);
    }

    public function getAllTravels()
    {
      return $this->repository->all();
    }

    public function store(array $data): Travel
    {
    //   dd($data);
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

