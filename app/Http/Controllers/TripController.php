<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RiderTaxiRequest;

class TripController extends Controller
{
    public function RiderRequestTaxi(RiderTaxiRequest $request){ 
        $validatedData = $request->validated();
        $validatedData['rider_id'] = Auth::id();
       
        $trip = Trip::create($validatedData);
       
        // Return a response (success message or the trip data)
        return response()->json([
            'message' => 'Trip request created successfully.',
            'trip' => $trip
        ], 201);  // HTTP Status Code 201 (Created)
    }
}
