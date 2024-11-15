<?php

namespace App\Http\Controllers;

use App\Contracts\TripInterface;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Models\Trip;
use App\Models\TaxiDriver;
use App\Events\RideRequested;
use App\Http\Requests\PriceRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redis;
use App\Http\Requests\RiderTaxiRequest;
use App\Http\Requests\StoreTripPriceRequest;
use App\Http\Resources\DriverDetailResources;
use App\Models\User;
use Exception;

class TripController extends Controller
{
    public $tripInterface;
    public function __construct(TripInterface $tripInterface)
    {
        $this->tripInterface = $tripInterface;
    }

    public function RiderRequestTaxi(RiderTaxiRequest $request)
    {
        $riderRequestedLocation = $request->validated();
        $riderRequestedLocation['rider_id'] = Auth::id();

        $trip = Trip::create($riderRequestedLocation);
        $drivers = $this->getNearbyDrivers($riderRequestedLocation);

        // Return a response (success message or the trip data)
        return response()->json([
            'message' => 'Trip request created successfully.',
            'trip' => $trip,
            'drivers' => $drivers
        ], 201);  // HTTP Status Code 201 (Created)
    }


    public function getNearbyDrivers($riderRequestedLocation)
    {
        $latitude = $riderRequestedLocation['current_location']['latitude'];
        $longitude = $riderRequestedLocation['current_location']['longitude'];
        $radius = 1; // 1km radius

        // Get nearby available drivers within 1km
        $drivers = TaxiDriver::nearby($latitude, $longitude, $radius)->get();

        // Broadcast the event to each driver on their respective channel
        foreach ($drivers as $driver) {
            broadcast(new RideRequested($driver->id, $riderRequestedLocation['current_location'], $riderRequestedLocation['destination'], $driver->id));
        }

        // Return nearby drivers in the response
        return $drivers;
    }

    public function storeDriverSettingPrice(StoreTripPriceRequest $storeTripPriceRequest)
    {
        $validatedData = $storeTripPriceRequest->validated();

        if (!Auth::check()) {
            return response()->json(['error' => 'User not authenticated'], 401);
        }

        try {
            $tripData = [
                'current_location' => $validatedData['current_location'],
                'destination' => $validatedData['destination'],
                'driver_id' => Auth::id(),
                'price' => $validatedData['price'],
            ];

            // Store the trip data in Redis as JSON in a list
            Redis::rpush('all_trip_data', json_encode($tripData));

            // Set expiration time for the Redis list
            Redis::expire('all_trip_data', 900);

            return response()->json([
                'message' => 'Trip data stored successfully',
                'data' => $tripData
            ], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function tripDifferentPrice()
    {
        try {
            // Retrieve all trips stored in the list
            $trips = Redis::lrange('all_trip_data', 0, -1);

            if (empty($trips)) {
                return response()->json(['error' => 'No trip data found'], 404);
            }

            // Decode JSON data
            $trips = array_map('json_decode', $trips);

            return response()->json(['trips' => $trips], 200);
        } catch (\Exception $e) {

            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

    public function driverDeatil($driver_id)
    {
        try {
            // Get all trip data from Redis
            $allTrips = Redis::lrange('all_trip_data', 0, -1);

            // Decode the JSON data and filter by driver_id
            $filteredTripsByTaxiDriver = array_filter($allTrips, function ($trip) use ($driver_id) {
                $tripData = json_decode($trip, true);
                return $tripData['driver_id'] == $driver_id;
            });

            $taxiDriverDatas =  $this->tripInterface->findwhere("TaxiDriver", $driver_id);

            $userData = $this->tripInterface->findUser("User", $driver_id);

            return new DriverDetailResources($filteredTripsByTaxiDriver, $taxiDriverDatas, $userData);
        } catch (Exception $e) {
            return response()->json([
                'error' => 'Something went wrong while fetching driver details.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
