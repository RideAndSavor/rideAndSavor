<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TravelService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\AcceptDriverService;
use App\Http\Requests\AcceptDriverRequest;
use App\Http\Resources\AcceptDriverResource;
use App\Services\BiddingPriceByDriverService;
use App\Services\NearbyTaxiService;

class AcceptDriverController extends Controller
{
    protected $acceptDriverService;
    protected $travelService;
    protected $biddingPriceByDriverService;
    protected $nearbyTaxiService;

    public function __construct(AcceptDriverService $acceptDriverService, TravelService $travelService, BiddingPriceByDriverService $biddingPriceByDriverService, NearbyTaxiService $nearbyTaxiService)
    {
        $this->acceptDriverService = $acceptDriverService;
        $this->travelService = $travelService;
        $this->biddingPriceByDriverService = $biddingPriceByDriverService;
        $this->nearbyTaxiService = $nearbyTaxiService;
    }

    // Get all accepted drivers
    public function index()
    {
        try {
            $acceptedDrivers = $this->acceptDriverService->getAllAcceptedDrivers();
            return response()->json(AcceptDriverResource::collection($acceptedDrivers)->toArray(request()), 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong!'], 500);
        }
    }

    public function store(AcceptDriverRequest $request)
    {
        try {
            // Validate and assign authenticated user's ID to the request data
            $validatedData = $request->validated();
            $validatedData['user_id'] = Auth::id(); // Add the authenticated user's ID

            // Update the travel status to 'accepted'
            $travel = $this->travelService->updateStatus($validatedData['travel_id'], 'accepted');

            if ($travel) {
                // Now delete the bidding entry from the bidding_prices table based on travel_id
                $this->biddingPriceByDriverService->deleteByTravelId($validatedData['travel_id']);
                // dd($validatedData['travel_id']);
                $this->nearbyTaxiService->deleteByTravelId($validatedData['travel_id']);

                // DB::table('nearby_taxi')->where('travel_id', $validatedData['travel_id'])->delete();

                $acceptedDriver = $this->acceptDriverService->store($validatedData);

                return response()->json([
                    'message' => 'Driver accepted and bidding entry deleted successfully!',
                    'data' => $acceptedDriver
                ], 200);
            }

            return response()->json(['error' => 'Travel not found or something went wrong!'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong while accepting the driver!'], 500);
        }
    }



    // Update an existing accepted driver
    public function update(AcceptDriverRequest $request, $id)
    {
        try {
            $validatedData = $request->validated();
            $acceptedDriver = $this->acceptDriverService->update($validatedData, $id);

            if ($acceptedDriver) {
                return response()->json(new AcceptDriverResource($acceptedDriver), 200);
            }

            return response()->json(['message' => 'Accepted driver not found'], 404);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong while updating!'], 500);
        }
    }

    // Delete an accepted driver
    public function destroy($id)
    {
        try {
            $this->acceptDriverService->delete($id);
            return response()->json(['message' => 'Accepted driver deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Something went wrong while deleting!'], 500);
        }
    }
}
