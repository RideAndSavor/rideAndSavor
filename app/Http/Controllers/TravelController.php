<?php

namespace App\Http\Controllers;

use App\Models\Travel;
use Illuminate\Http\Request;
use App\Services\TravelService;
use Illuminate\Http\JsonResponse;
use App\Contracts\TravelInterface;
use App\Http\Requests\TravelRequest;
use App\Http\Resources\TravelResource;
use App\Services\NearbyTaxiService;
use Illuminate\Support\Facades\Auth;

class TravelController extends Controller
{
    protected $travelService;
    protected $nearByTaxiService;
    public function __construct(TravelService $travelService,NearbyTaxiService $nearByTaxiService)
    {
        $this->travelService = $travelService;
        $this->nearByTaxiService = $nearByTaxiService;
    }

    /**
     * Get all travel records.
     */
    public function index()
    {
        $travels = $this->travelService->getAllTravels();
        return response()->json(TravelResource::collection($travels));
    }

    /**
     * Store a new travel record.
     */
    public function store(TravelRequest $request)
    {
        // Validate the request data
        $validateData = $request->validated();
        $validateData['user_id']= Auth::id();

        $travel = $this->travelService->store($validateData);

        $latitude = $travel->pickup_latitude;
        $longitude = $travel->pickup_longitude;
        $radius = 1;

        $nearbyDrivers = $this->nearByTaxiService->getNearbyDrivers($latitude, $longitude, $radius);

        // Store nearby drivers in the database using the repository
        $this->nearByTaxiService->storeNearbyDrivers($travel->id, $nearbyDrivers);

        // Return the response with the stored travel data and nearby drivers
        return response()->json([
            'travel' => new TravelResource($travel), // Returning the travel data
            'nearby_drivers' => $nearbyDrivers,     // Returning the nearby drivers data
        ], 201);
    }



    /**
     * Update a travel record.
     */
    public function update(TravelRequest $request, $id): JsonResponse
    {
        $travel = $this->travelService->update($request->validated(), $id);
        return response()->json(new TravelResource($travel));
    }

    /**
     * Delete a travel record.
     */
    public function destroy($id): JsonResponse
    {
        $this->travelService->delete($id);
        return response()->json(['message' => 'Travel record deleted successfully'], 200);
    }
}
