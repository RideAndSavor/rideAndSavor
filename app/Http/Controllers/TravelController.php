<?php

namespace App\Http\Controllers;

use App\Contracts\TravelInterface;
use App\Http\Requests\TravelRequest;
use App\Http\Resources\TravelResource;
use App\Models\Travel;
use App\Services\TravelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TravelController extends Controller
{
    protected $travelService;
    public function __construct(TravelService $travelService)
    {
        $this->travelService = $travelService;
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
        // dd($request);
        $validateData = $request->validated();
        // dd($validateData);
        $travel = $this->travelService->store($validateData);
        return response()->json(new TravelResource($travel), 201);
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
