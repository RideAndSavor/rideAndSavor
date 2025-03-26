<?php

namespace App\Services;

use App\Contracts\BiddingPriceByDriverInterface;
use App\Models\BiddingPriceByDriver;

class BiddingPriceByDriverService
{
    protected $repository;
    protected $travelService;

    public function __construct(BiddingPriceByDriverInterface $repository,TravelService $travelService)
    {
        $this->repository = $repository;
        $this->travelService = $travelService;
    }

    public function getAllBiddingPrices()
    {
        // dd("ok");
        // dd($this->repository->all());
        return $this->repository->all();
    }

    public function store(array $data): BiddingPriceByDriver
    {
         // Assuming you are passing 'travel_id' in the request
        $this->travelService->updateStatus($data['travel_id'], 'bidding');

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

    public function getBiddingPricesByTravelId($travelId)
    {
        return $this->repository->getBiddingPricesByTravelId($travelId);
    }


}
