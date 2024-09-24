<?php
namespace App\Http\Controllers;

use App\Models\TaxiDriver;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaxiDriverController extends Controller
{
    // Update the driver's location
    public function updateLocation(Request $request)
    {
        $request->validate([
            'current_location.lat' => 'required|numeric|between:-90,90',
            'current_location.long' => 'required|numeric|between:-180,180',
        ]);
    
        // Get the authenticated driver (assuming they're logged in)
        $user_id = Auth::id();
    
        // Update the driver's current location
        TaxiDriver::where('user_id', $user_id)->update([
            'current_location' => $request->input('current_location'),
        ]);
    
        // Retrieve the updated driver record
        $updatedDriver = TaxiDriver::where('user_id', $user_id)->first();
    
        return response()->json([
            'message' => 'Location updated successfully',
            'data' => $updatedDriver,  // Return the updated driver record with the new location
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
