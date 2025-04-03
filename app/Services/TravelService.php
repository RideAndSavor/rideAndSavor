<?php

namespace App\Services;

use App\Models\Travel;
use App\Contracts\TravelInterface;
use App\Services\NearbyTaxiService;

class TravelService
{
    protected $repository;
    protected $nearbyTaxiService;

    public function __construct(TravelInterface $repository, NearbyTaxiService $nearByTaxiService)
    {
        $this->repository = $repository;
        $this->nearbyTaxiService = $nearByTaxiService;
    }


    public function updateStatus(int $travelId, string $status)
    {
        return $this->repository->updateStatus($travelId, $status);
    }

    public function getAllTravels()
    {
      return $this->repository->all();
    }

    public function getById(int $id)
    {
        return $this->repository->getById($id);
    }

    public function store(array $data): Travel
    {
      return $this->repository->store($data);
    }

    public function getNearbyDriversForTravel(Travel $travel, $radius = 1)
    {
        $latitude = $travel->pickup_latitude;
        $longitude = $travel->pickup_longitude;

        return $this->nearbyTaxiService->getNearbyDrivers($latitude, $longitude, $radius);
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

