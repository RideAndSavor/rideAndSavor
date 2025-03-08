<?php

namespace App\Services;

use App\Models\NearbyTaxi;
use App\Contracts\NearbyTaxiInterface;

class NearbyTaxiService
{
    protected $repository;

    public function __construct(NearbyTaxiInterface $repository)
    {
        $this->repository = $repository;
    }

    public function store(array $data): NearbyTaxi
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

    public function deleteByTravelId(int $travelId)
    {
        return $this->repository->deleteByTravelId($travelId);
    }

    public function getNearbyDrivers($latitude, $longitude, $radius = 1)
    {
        return $this->repository->getNearbyDrivers($latitude, $longitude, $radius);
    }

    public function storeNearbyDrivers($travelId, $nearbyDrivers)
    {
        $this->repository->storeNearbyDrivers($travelId, $nearbyDrivers);
    }
}
