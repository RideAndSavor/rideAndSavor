<?php

namespace App\Services;

use App\Contracts\BiddingPriceByDriverInterface;
use App\Models\BiddingPriceByDriver;

class BiddingPriceByDriverService
{
    protected $repository;

    public function __construct(BiddingPriceByDriverInterface $repository)
    {
        $this->repository = $repository;
    }

    public function getAllBiddingPrices()
    {
        // dd("ok");
        // dd($this->repository->all());
        return $this->repository->all();
    }

    public function store(array $data): BiddingPriceByDriver
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
        // Delete the bidding price entry for the given travel_id
        return $this->repository->deleteByTravelId($travelId);
    }

}
