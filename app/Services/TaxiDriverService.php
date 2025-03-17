<?php
namespace App\Services;

use App\Models\TaxiDriver;
use App\Repositories\TaxiDriverRepository;

class TaxiDriverService
{
    protected $taxiDriverRepository;

    public function __construct(TaxiDriverRepository $taxiDriverRepository)
    {
        $this->taxiDriverRepository = $taxiDriverRepository;
    }

    public function getAllTaxiDriver()
    {
        // dd("ok");
        // dd($this->repository->all());
        return $this->taxiDriverRepository->all();
    }

    public function store(array $data): TaxiDriver
    {
        return $this->taxiDriverRepository->store($data);
    }

    public function update(array $data, int $id)
    {
        // dd($data);
        return $this->taxiDriverRepository->update($data, $id);
    }

    public function delete(int $id)
    {
        $this->taxiDriverRepository->delete($id);
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

    public function getByUserId($user_id)
    {
            // dd("OK");
        return TaxiDriver::where('user_id', $user_id)->with('user')->first();
    }

}

