<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\TaxiDriver;
use App\Events\RideRequested;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RiderTaxiRequest;

class TripController extends Controller
{
    public function RiderRequestTaxi(RiderTaxiRequest $request){ 
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
}
