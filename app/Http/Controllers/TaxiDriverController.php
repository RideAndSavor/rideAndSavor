<?php

namespace App\Http\Controllers;

use App\Events\trackingDriverCurrentLocation;
use App\Http\Requests\TaxiDriverRequest;
use App\Http\Resources\DriverNotificationResource;
use App\Http\Resources\TaxiDriverResource;
use App\Models\User;
use App\Services\TaxiDriverService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class TaxiDriverController extends BaseController
{
    protected $taxiDriverService;

    public function __construct(TaxiDriverService $taxiDriverService)
    {
        $this->taxiDriverService = $taxiDriverService;
    }

    /**
     * Get all taxi drivers.
     */
    public function index()
    {
        return $this->handleRequest(function () {
            $taxi_drivers = $this->taxiDriverService->getAllTaxiDriver();
            return response()->json(TaxiDriverResource::collection($taxi_drivers)->toArray(request()), Config::get('variable.OK'));
        });
    }

    /**
     * Get a single taxi driver by ID.
     */
    public function show($id)
    {
        return $this->handleRequest(function () use ($id) {
            $taxi_driver = $this->taxiDriverService->getById($id);

            if (!$taxi_driver) {
                return response()->json(['message' => Config::get('variable.TAXI_DRIVER_NOT_FOUND')],Config::get('variable.SEVER_NOT_FOUND'));
            }

            return new TaxiDriverResource($taxi_driver);
        });
    }

    public function getByUserId($user_id)
{
    return $this->handleRequest(function () use ($user_id) {
        $taxi_driver = $this->taxiDriverService->getByUserId($user_id);

        if (!$taxi_driver) {
            return response()->json(['message' => Config::get('variable.TAXI_DRIVER_NOT_FOUND'),
        'data' => []]);
        }

        return new TaxiDriverResource($taxi_driver);
    });
}


    /**
     * Store a new taxi driver with car information.
     */
    public function store(TaxiDriverRequest $taxiDriverRequest)
    {
        return $this->handleRequest(function () use ($taxiDriverRequest) {
            $validatedData = $taxiDriverRequest->validated();
            $validatedData['user_id'] = Auth::id();

            $taxi_driver = $this->taxiDriverService->store($validatedData);

        // Retrieve the user model and update the role
        User::where('id', Auth::id())->update(['role' => 5]);

            // Wrap the TaxiDriverResource in a JsonResponse
            return response()->json(new TaxiDriverResource($taxi_driver), Config::get('variable.CREATED'));
        });
    }


    /**
     * Update a taxi driver with car information.
     */
    public function update(TaxiDriverRequest $request, $id)
    {
        return $this->handleRequest(function () use ($request, $id) {
            $validatedData = $request->validated();
            $taxiDriver = $this->taxiDriverService->update($validatedData, $id);

            if (!$taxiDriver) {
                return response()->json(['message' => Config::get('variable.TAXI_DRIVER_NOT_FOUND')], status: Config::get('variable.SEVER_NOT_FOUND'));
            }

            return response()->json(new TaxiDriverResource($taxiDriver), Config::get('variable.OK'));
        });
    }

    /**
     * Delete a taxi driver.
     */
    public function destroy($id)
    {
        return $this->handleRequest(function () use ($id) {
            $this->taxiDriverService->delete($id);
            return response()->json(['message' => Config::get('variable.TAXI_DRIVER_DELETED_SUCCESSFULLY')], 200);
        });
    }

    /**
     * Update the driver's location.
     */
    public function updateLocation(Request $request)
    {
        return $this->handleRequest(function () use ($request) {
            $validatedData = $request->validate([
                'driver_id' => 'required',
                'current_location.lat' => 'required|numeric|between:-90,90',
                'current_location.long' => 'required|numeric|between:-180,180',
                'is_available' => 'required|boolean'
            ]);

            event(new trackingDriverCurrentLocation($validatedData));

            return response()->json(['message' => Config::get('variable.TAXI_DRIVER_UPDATED_SUCCESSFULLY')]);
        });
    }

    /**
     * Get notifications for a driver.
     */
    public function getDriverNotifications($driverId)
    {
        return $this->handleRequest(function () use ($driverId) {
            $notifications = $this->taxiDriverService->getDriverNotifications($driverId);
            return response()->json(DriverNotificationResource::collection($notifications));
        });
    }
}
