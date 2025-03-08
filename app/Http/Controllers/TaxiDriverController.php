<?php
namespace App\Http\Controllers;

use Throwable;
use App\Models\NearbyTaxi;
use App\Models\TaxiDriver;
use Illuminate\Http\Request;
use App\Events\RideRequested;
use App\Exceptions\CrudException;
use App\Services\TaxiDriverService;
use Illuminate\Support\Facades\Log;
use App\Contracts\LocationInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\TaxiDriverRequest;
use App\Http\Resources\TaxiDriverResource;
use App\Events\trackingDriverCurrentLocation;
use App\Http\Resources\DriverNotificationResource;

class TaxiDriverController extends Controller
{
    private $taxi_driverInterface;
    protected $taxiDriverService;

    public function __construct(LocationInterface $taxi_driverInterface,TaxiDriverService $taxiDriverService)
    {
        $this->taxi_driverInterface = $taxi_driverInterface;
        $this->taxiDriverService = $taxiDriverService;
    }

    public function index()
    {

        $taxi_drivers = $this->taxi_driverInterface->all('TaxiDriver');
        return response()->json(TaxiDriverResource::collection($taxi_drivers)->toArray(request()), 200);
    }

    public function show($id)
    {
        try {
            // Fetch the taxi driver using the repository
            $taxi_driver = $this->taxiDriverService->getById($id);

            if (!$taxi_driver) {
                return response()->json(['message' => 'Driver not found'], 404);
            }

            return new TaxiDriverResource($taxi_driver);
        } catch (Throwable $th)
        {
            throw CrudException::argumentCountError();
        }
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
        // dd($request);
        $validatedData = $request->validated();
        // dd($validatedData);
        $taxi_driver = $this->taxi_driverInterface->findById('TaxiDriver', $id);
        // dd($taxi_driver);
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
        // dd($request);
        $validatedData = $request->validate([
            'driver_id' => 'required',
            'current_location.lat' => 'required|numeric|between:-90,90',
            'current_location.long' => 'required|numeric|between:-180,180',
            'is_available'=> 'required|boolean'
        ]);
        // dd($validatedData);

        event(new trackingDriverCurrentLocation($validatedData));

        return response()->json([
            'message' => "Driver's Current Location updated successfully",
        ]);

    }

    public function getDriverNotifications($driverId)
    {
        $notifications = $this->taxiDriverService->getDriverNotifications($driverId);
        return response()->json( DriverNotificationResource::collection($notifications));
    }
}
