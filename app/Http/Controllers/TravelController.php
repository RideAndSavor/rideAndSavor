<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Services\TravelService;
use App\Services\NearbyTaxiService;
use App\Http\Requests\TravelRequest;
use App\Http\Resources\TravelResource;

class TravelController extends BaseController
{
    protected $travelService;
    protected $nearByTaxiService;

    public function __construct(TravelService $travelService,NearByTaxiService $nearByTaxiService)
    {
        $this->travelService = $travelService;
        $this->nearByTaxiService = $nearByTaxiService;
    }

    /**
     * Get all travel records.
     */
    public function index(): JsonResponse
    {
        return $this->handleRequest(function () {
            $travels = $this->travelService->getAllTravels();
            return response()->json(TravelResource::collection($travels));
        });
    }

    /**
     * Store a new travel record.
     */
    public function store(TravelRequest $request): JsonResponse
    {
        return $this->handleRequest(function () use ($request) {
            $validateData = $request->validated();
            $validateData['user_id'] = Auth::id();

            $travel = $this->travelService->store($validateData);

            // Get nearby drivers using the TravelService
            $nearbyDrivers = $this->travelService->getNearbyDriversForTravel($travel);

            // Store nearby drivers in the database using the repository
            $this->nearByTaxiService->storeNearbyDrivers($travel->id, $nearbyDrivers);

            return response()->json([
                'travel' => new TravelResource($travel),
                'nearby_drivers' => $nearbyDrivers,
            ], 201);
        });
    }

    /**
     * Update a travel record.
     */
    public function update(TravelRequest $request, $id): JsonResponse
    {
        return $this->handleRequest(function () use ($request, $id) {
            $travel = $this->travelService->update($request->validated(), $id);
            return response()->json(new TravelResource($travel));
        });
    }

    /**
     * Delete a travel record.
     */
    public function destroy($id): JsonResponse
    {
        return $this->handleRequest(function () use ($id) {
            $this->travelService->delete($id);
            return response()->json(['message' => 'Travel record deleted successfully'], 200);
        });
    }
}
