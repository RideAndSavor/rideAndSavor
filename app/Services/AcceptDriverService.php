<?php

namespace App\Services;

use App\Models\TaxiDriver;
use App\Models\AcceptDriver;
use App\Services\TravelService;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use App\Services\NearbyTaxiService;
use App\Contracts\AcceptDriverInterface;
use App\Services\BiddingPriceByDriverService;

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
            // Check driver availability first (This is now redundant, but keep it for safety if needed)
            $driver = TaxiDriver::findOrFail($data['taxi_driver_id']);

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

            $driver->update(['is_available' => 0]);

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

    public function getDriverNotifications($driverId)
    {
        // Fetch notifications where driver_id matches and status is 'pending'
        $notifications = AcceptDriver::where('taxi_driver_id', $driverId)
            ->where('status', 'pending')
            ->select('user_id', 'travel_id', 'id')
            ->get();

        return $notifications;
    }

    public function completeTravel(int $travelId): bool
    {
        return DB::transaction(function () use ($travelId) {
            // Find the accepted driver entry based on travel_id
            $acceptedDriver = AcceptDriver::where('travel_id', $travelId)->first();

            if (!$acceptedDriver) {
                throw new CustomException("Travel not found.");
            }

            // Update trip status to completed
            $acceptedDriver->update(['status' => 'completed']);

            // Mark the driver as available again
            $driver = TaxiDriver::find($acceptedDriver->taxi_driver_id);
            if ($driver) {
                $driver->update(['is_available' => 1]);
            }

            return true;
        });
    }

}


