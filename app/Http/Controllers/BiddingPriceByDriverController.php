<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BiddingPriceRequest;
use App\Http\Resources\TaxiDriverResource;
use App\Http\Resources\BiddingPriceResource;
use App\Models\NearbyTaxi;
use App\Services\BiddingPriceByDriverService;

class BiddingPriceByDriverController extends BaseController
{
    protected $biddingPriceByDriverService;

    public function __construct(BiddingPriceByDriverService $biddingPriceByDriverService)
    {
        $this->biddingPriceByDriverService = $biddingPriceByDriverService;
    }

    /**
     * Get all bidding prices.
     */
    public function index()
    {
        return $this->handleRequest(function () {
            $biddingPrices = $this->biddingPriceByDriverService->getAllBiddingPrices();
            return response()->json(BiddingPriceResource::collection($biddingPrices)->toArray(request()), 200);
        });
    }

    /**
     * Store a new bidding price.
     */
    public function store(BiddingPriceRequest $request)
    {
        return $this->handleRequest(function () use ($request) {
            $validatedData = $request->validated();
            $biddingPrice = $this->biddingPriceByDriverService->store($validatedData);
            // Delete the nearby_taxi record for this travel_id and taxi_driver_id
        NearbyTaxi::where('travel_id', $validatedData['travel_id'])
        ->where('taxi_driver_id', $validatedData['taxi_driver_id'])
        ->delete();
            return response()->json(new BiddingPriceResource($biddingPrice), 201);
        });
    }

    /**
     * Update an existing bidding price.
     */
    public function update(BiddingPriceRequest $request, $id)
    {
        return $this->handleRequest(function () use ($request, $id) {
            $validatedData = $request->validated();
            $biddingPrice = $this->biddingPriceByDriverService->update($validatedData, $id);

            if (!$biddingPrice) {
                return response()->json(['message' => 'Bidding price not found'], 404);
            }

            return response()->json(new BiddingPriceResource($biddingPrice), 200);
        });
    }

    /**
     * Delete a bidding price.
     */
    public function destroy($id)
    {
        return $this->handleRequest(function () use ($id) {
            $this->biddingPriceByDriverService->delete($id);
            return response()->json(['message' => 'Bidding price deleted successfully'], 200);
        });
    }

    /**
     * Get all bidding prices by user ID.
     */
    public function getBiddingPricesByTravelId($travelId)
{
    try {
        $biddingPrices = $this->biddingPriceByDriverService->getBiddingPricesByTravelId($travelId);

        if ($biddingPrices->isEmpty()) {
            return response()->json([['message' => 'No bids found for this trip'], 'data' => []]);
        }

        $data = $biddingPrices->map(function ($biddingPrice) {
            return [
                'bidding_price' => new BiddingPriceResource($biddingPrice),
                'driver' => new TaxiDriverResource($biddingPrice->driver) // Access the associated driver
            ];
        });

        return response()->json($data, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Something went wrong while retrieving bids!'], 500);
    }
}

}

