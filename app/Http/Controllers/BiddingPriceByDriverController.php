<?php

namespace App\Http\Controllers;

use App\Services\BiddingPriceByDriverService;
use App\Http\Requests\BiddingPriceRequest;
use App\Http\Resources\BiddingPriceResource;

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
}
