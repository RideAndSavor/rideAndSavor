<?php

namespace App\Http\Controllers;

use App\Services\BiddingPriceByDriverService;
use App\Http\Requests\BiddingPriceRequest;
use App\Http\Resources\BiddingPriceResource;
use App\Services\TravelService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class BiddingPriceByDriverController extends Controller
{
    protected $biddingPriceByDriverService;
    protected $travelService;

    public function __construct(BiddingPriceByDriverService $biddingPriceByDriverService,TravelService $travelService)
    {
        // dd("ok");
        $this->biddingPriceByDriverService = $biddingPriceByDriverService;
        $this->travelService = $travelService;
    }

    // Get all bidding prices
    public function index()
    {
        // dd("ok");
        try {
            $biddingPrices = $this->biddingPriceByDriverService->getAllBiddingPrices();
            // dd($biddingPrices);
            return response()->json(BiddingPriceResource::collection($biddingPrices)->toArray(request()), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!'], 500);
        }

        $biddingPrices = $this->biddingPriceByDriverService->getAllBiddingPrices()?? collect([]);
        // dd($biddingPrices);
            return response()->json(BiddingPriceResource::collection($biddingPrices)->toArray(request()), 200);
    }

    // Store a new bidding price
    public function store(BiddingPriceRequest $request)
    {
        try {
            $validatedData = $request->validated();

            // Store the bidding price first
            $biddingPrice = $this->biddingPriceByDriverService->store($validatedData);

            // Now, update the status to "bidding" for the associated travel
            // Assuming you are passing 'travel_id' in the request
            $this->travelService->updateStatus($validatedData['travel_id'], 'bidding');

            return response()->json(new BiddingPriceResource($biddingPrice), 201);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong while storing!'], 500);
        }
    }


    // Update an existing bidding price
    public function update(BiddingPriceRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $biddingPrice = $this->biddingPriceByDriverService->update($validatedData, $id);

            if ($biddingPrice) {
                return response()->json(new BiddingPriceResource($biddingPrice), 200);
            }

            return response()->json(['message' => 'Bidding price not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong while updating!'], 500);
        }
    }

    // Delete a bidding price
    public function destroy($id)
    {
        try {
            $this->biddingPriceByDriverService->delete($id);
            return response()->json(['message' => 'Bidding price deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong while deleting!'], 500);
        }
    }
}
