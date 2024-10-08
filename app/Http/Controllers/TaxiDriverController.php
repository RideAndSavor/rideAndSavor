<?php
namespace App\Http\Controllers;

use App\Models\TaxiDriver;
use Illuminate\Http\Request;
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
            'current_location.lat' => 'required|numeric|between:-90,90',
            'current_location.long' => 'required|numeric|between:-180,180',
        ]);

        $latitude = $validatedData['current_location']['lat'];
        $longitude = $validatedData['current_location']['long'];
        $radius = 1; // Default to 1km
        
        // Get nearby available drivers
        $drivers = TaxiDriver::nearby($latitude, $longitude, $radius)->get();
        
        return response()->json($drivers);
    }

}
