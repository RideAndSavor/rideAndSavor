<?php
namespace App\Services;

use App\Repositories\TaxiDriverRepository;

class TaxiDriverService
{
    protected $taxiDriverRepository;

    public function __construct(TaxiDriverRepository $taxiDriverRepository)
    {
        $this->taxiDriverRepository = $taxiDriverRepository;
    }

    public function getDriverNotifications($driverId)
    {
        return $this->taxiDriverRepository->getPendingRidesForDriver($driverId)
            ->load(['travel.user']); // Eager load travel and its user relationship
    }
    public function getById(int $id)
    {
        return $this->taxiDriverRepository->getById($id);
    }
}

