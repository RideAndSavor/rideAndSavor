<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\AcceptDriverService;
use App\Http\Requests\AcceptDriverRequest;
use App\Http\Resources\AcceptDriverResource;
use App\Http\Resources\DriverNotificationResource;


class AcceptDriverController extends Controller
{
    protected $acceptDriverService;

    public function __construct(AcceptDriverService $acceptDriverService)
    {
        $this->acceptDriverService = $acceptDriverService;
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
            // Store the accepted driver
            $acceptedDriver = $this->acceptDriverService->store($validatedData);

            return response()->json([
                'message' => 'Driver accepted and bidding entry deleted successfully!',
                'data' => $acceptedDriver
            ], 200);

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

    public function getDriverHistory($driverId)
    {
        try {
            $notifications = $this->acceptDriverService->getDriverHistory($driverId);

            return response()->json(DriverNotificationResource::collection($notifications));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch notifications'], 500);
        }
    }


    public function getNotiForDriver($driverId, $travelId)
    {
        try {
            $notifications = $this->acceptDriverService->getDriverNotifications($driverId, $travelId);

            return response()->json(DriverNotificationResource::collection($notifications));
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch notifications'], 500);
        }
    }
}
