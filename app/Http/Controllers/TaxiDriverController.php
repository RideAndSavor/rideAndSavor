<?php
namespace App\Http\Controllers;

use Throwable;
use App\Models\TaxiDriver;
use Illuminate\Http\Request;
use App\Events\RideRequested;
use App\Exceptions\CrudException;
use Illuminate\Support\Facades\Log;
use App\Contracts\LocationInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\TaxiDriverRequest;
use App\Http\Resources\TaxiDriverResource;
use App\Events\trackingDriverCurrentLocation;

class TaxiDriverController extends Controller
{
    private $taxi_driverInterface;

    public function __construct(LocationInterface $taxi_driverInterface)
    {
        $this->taxi_driverInterface = $taxi_driverInterface;
    }

    public function index()
    {

        $taxi_drivers = $this->taxi_driverInterface->all('TaxiDriver');
        return TaxiDriverResource::collection($taxi_drivers);
    }

    // Add taxi_Driver with car information
    public function store(TaxiDriverRequest $taxiDriverRequest)
    {
        $validatedData = $taxiDriverRequest->validated();
        $validatedData['user_id'] = Auth::id();
        try
        {
            $taxi_driver = $this->taxi_driverInterface->store('TaxiDriver', $validatedData);
            return new TaxiDriverResource($taxi_driver);
        }catch (Throwable $th)
        {
            throw CrudException::argumentCountError();
        }
    }

    /* Update taxi_Driver with car information */
    public function update(TaxiDriverRequest $request, string $id)
    {
        $validatedData = $request->validated();
        // dd($validatedData);
        $taxi_driver = $this->taxi_driverInterface->findById('TaxiDriver', $id);
        if (!$taxi_driver) {
            return response()->json([
                'message' => Config::get('variable.TAXI_DRIVER_NOT_FOUND')
            ], Config::get('variable.SERVER_ERROR'));
        }
        $updatedTaxiDriver = $this->taxi_driverInterface->update('TaxiDriver', $validatedData, $id);
        return new TaxiDriverResource($updatedTaxiDriver);
    }

    /* Delete taxi_Driver with car information */
    public function destroy(string $id)
    {
        $taxi_driver = $this->taxi_driverInterface->findById('TaxiDriver', $id);
        if(!$taxi_driver)
        {
            return response()->json(['message' => Config::get('variable.TAXI_DRIVER_NOT_FOUND')],Config::get('variable.SEVER_ERROR'));
        }
        $this->taxi_driverInterface->delete('TaxiDriver', $id);
        return response()->json([
            'message'=>Config::get('variable.TAXI_DRIVER_DELETED_SUCCESSFULLY')
        ],Config::get('variable.NO_CONTENT'));
    }

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
