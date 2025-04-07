<?php

namespace App\Http\Controllers;

use App\Models\Travel;
use App\Models\TaxiDriver;
use App\Models\AcceptDriver;
use Illuminate\Http\Request;
use App\Services\TravelService;
use Illuminate\Support\Facades\DB;
use App\Exceptions\CustomException;
use Illuminate\Support\Facades\Auth;
use App\Services\AcceptDriverService;
use App\Http\Resources\TravelResource;
use App\Http\Requests\AcceptDriverRequest;
use App\Http\Resources\AcceptDriverResource;
use App\Http\Resources\DriverNotificationResource;

class AcceptDriverController extends Controller
{
    protected $acceptDriverService;
    protected $travelService;

    public function __construct(AcceptDriverService $acceptDriverService,TravelService $travelService)
    {
        $this->acceptDriverService = $acceptDriverService;
        $this->travelService = $travelService;
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
            $validatedData = $request->validated();
            $validatedData['user_id'] = Auth::id();

            $driver = TaxiDriver::findOrFail($validatedData['taxi_driver_id']);
            if ($driver->is_available === 0) {
                return response()->json([
                    'message' => 'This driver is not available, choose another driver!',
                    'data' => []
                ], 200);
            }

            $acceptedDriver = $this->acceptDriverService->store($validatedData);

            return response()->json([
                'message' => 'Driver accepted and bidding entry deleted successfully!',
                'data' => $acceptedDriver
            ], 200);

        } catch (CustomException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
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

    public function getDriverHistory($driverId)
    {
        try {
            $notifications = $this->acceptDriverService->getDriverHistory($driverId);

            return response()->json(DriverNotificationResource::collection($notifications));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch notifications'], 500);
        }
    }

    public function getNotiForDriver($driverId)
    {
        try {
            $notifications = $this->acceptDriverService->getDriverNotifications($driverId);

            return response()->json(DriverNotificationResource::collection($notifications));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch notifications'], 500);
        }
    }

    public function updateDriverStatus(Request $request)
    {
        $request->validate([
            'travel_id' => 'required|integer',
        ]);

        try {
            $driverId = Auth::id();
            $taxiDriver = TaxiDriver::where('user_id', $driverId)->firstOrFail(['id']);
            $taxiDriverId = $taxiDriver->id;

            $notification = AcceptDriver::where('taxi_driver_id', $taxiDriverId)
                ->where('travel_id', $request->travel_id)
                ->where('status', 'pending')
                ->firstOrFail();

            $notification->update(['status' => 'accepted']);

            $travel = $this->travelService->getById($request->travel_id);

            if (!$travel) {
                return response()->json(['message' => 'Travel not found'], 404);
            }

            return response()->json([
                'message' => "Status updated successfully to accepted",
                'data' => new AcceptDriverResource($notification),
                'travel_data' => new TravelResource($travel),
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update status or notification not found'], 500);
        }
    }


    public function showDriverComplete($travel_id)
{
    try {
        $acceptDriver = AcceptDriver::where('travel_id', $travel_id)
            ->where('status', 'completed')
            ->first();

        if (!$acceptDriver) {
            return response()->json([
                'message' => 'No accepted driver status found for the provided travel ID',
                'data' => []
            ]);
        }

        return response()->json([
            'message' => 'Data retrieved successfully',
            'data' => new AcceptDriverResource($acceptDriver),
        ], 200);

    } catch (\Exception $e) {
        return response()->json(['error' => 'An error occurred while retrieving data'], 500);
    }
}






    public function completeTravel($travelId)
    {
        try {
            $completed = $this->acceptDriverService->completeTravel($travelId);

            if ($completed) {
                return response()->json([
                    'message' => 'Travel completed successfully. Driver is now available for the next trip.'
                ], 200);
            }

            return response()->json([
                'message' => 'Failed to complete trip.'
            ], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
