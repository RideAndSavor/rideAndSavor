<?php
namespace App\Http\Controllers;

use App\Models\TaxiDriver;
use Illuminate\Http\Request;
use App\Events\RideRequested;
use Illuminate\Support\Facades\Auth;
use App\Events\trackingDriverCurrentLocation;

class TaxiDriverController extends Controller
{
    // Update the driver's location
    public function updateLocation(Request $request)
    {
        $validatedData = $request->validate([
            'driver_id' => 'required',
            'current_location.lat' => 'required|numeric|between:-90,90',
            'current_location.long' => 'required|numeric|between:-180,180',
        ]);

        event(new trackingDriverCurrentLocation($validatedData));
    
        return response()->json([
            'message' => "Driver's Current Location updated successfully",
        ]);
    
    }

    public function getNearbyDrivers(Request $request)
{
    $validatedData = $request->validate([
        'rider_id' => 'required|string',
        'current_location.lat' => 'required|numeric|between:-90,90',
        'current_location.long' => 'required|numeric|between:-180,180',
        'destination.lat' => 'required|numeric|between:-90,90',
        'destination.long' => 'required|numeric|between:-180,180',
    ]);

    $latitude = $validatedData['current_location']['lat'];
    $longitude = $validatedData['current_location']['long'];
    $radius = 1; // 1km radius
    
    // Get nearby available drivers within 1km
    $drivers = TaxiDriver::nearby($latitude, $longitude, $radius)->get();
    
    // Broadcast event to each nearby driver
    foreach ($drivers as $driver) {
        // Notify each driver individually
        // event(new RideRequested($validatedData['2']->current_location, $validatedData['2']->destination))
        //     ->toOthers()
        //     ->onChannel('driver.' . $driver->id); // Dynamic channel for each driver
    }
    
    // Return nearby drivers in the response
    return response()->json($drivers);
}


    // public function getNearbyDrivers(Request $request)
    // {
    //     $validatedData = $request->validate([
    //         'rider_id' => 'required|string',
    //         'current_location.lat' => 'required|numeric|between:-90,90',
    //         'current_location.long' => 'required|numeric|between:-180,180',
    //         'destination.lat' => 'required|numeric|between:-90,90',
    //         'destination.long' => 'required|numeric|between:-180,180',
    //     ]);

    //     $latitude = $validatedData['current_location']['lat'];
    //     $longitude = $validatedData['current_location']['long'];
    //     $radius = 1; // Default to 1km
        
    //     // Get nearby available drivers
    //     $drivers = TaxiDriver::nearby($latitude, $longitude, $radius)->get();
        
    //     // Broadcast event to nearby drivers
    //     event(new RideRequested($validatedData['current_location'], $validatedData['destination']));
        
    //     return response()->json($drivers);
    // }

}
