<?php

namespace App\Services;

use App\Models\AcceptDriver;
use App\Services\TravelService;
use App\Services\NearbyTaxiService;
use App\Contracts\AcceptDriverInterface;
use App\Services\BiddingPriceByDriverService;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;

class AcceptDriverService
{
    protected $repository;
    protected $travelService;
    protected $biddingPriceByDriverService;
    protected $nearbyTaxiService;

    public function __construct(
        AcceptDriverInterface $repository,
        TravelService $travelService,
        BiddingPriceByDriverService $biddingPriceByDriverService,
        NearbyTaxiService $nearbyTaxiService
        )
    {
        $this->repository = $repository;
        $this->travelService = $travelService;
        $this->biddingPriceByDriverService = $biddingPriceByDriverService;
        $this->nearbyTaxiService = $nearbyTaxiService;
    }

    public function getAllAcceptedDrivers()
    {
        return $this->repository->all();
    }



    public function store(array $data): AcceptDriver
    {
        return DB::transaction(function () use ($data) {
            // Update the travel status to 'accepted'
            if (!$this->travelService->updateStatus($data['travel_id'], 'accepted')) {
                throw new CustomException("Failed to update travel status");
            }

            // Delete bidding entry
            if (!$this->biddingPriceByDriverService->deleteByTravelId($data['travel_id'])) {
                throw new CustomException("Failed to delete bidding price entry");
            }

            // Delete nearby taxi entries
            if (!$this->nearbyTaxiService->deleteByTravelId($data['travel_id'])) {
                throw new CustomException("Failed to delete nearby taxi entries");
            }

            // Store the accepted driver entry
            return $this->repository->store($data);
        });
    }


    public function update(array $data, int $id)
    {
        return $this->repository->update($data, $id);
    }

    public function delete(int $id)
    {
        $this->repository->delete($id);
    }


    public function getDriverHistory($driverId)
    {
        $notifications = AcceptDriver::where('taxi_driver_id', $driverId)
            ->select('user_id', 'travel_id', 'id')
            ->get();

        return $notifications;
    }

    public function getDriverNotifications($driverId, $travelId)
    {

            // Fetch notifications where both driver_id and travel_id match the given parameters
            $notifications = AcceptDriver::where('taxi_driver_id', $driverId)
                ->where('travel_id', $travelId)
                ->select('user_id', 'travel_id', 'id')
                ->get();

            // if ($notifications->isEmpty()) {
            //     return response()->json(['message' => 'No matching notifications found.'], 404);
            // }

            return $notifications;
        }
    }


