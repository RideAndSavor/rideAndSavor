<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Address;
use App\Models\Restaurant;
use Illuminate\Support\Arr;
use App\Traits\AddressTrait;
use Illuminate\Http\Request;
use App\Exceptions\CrudException;
use Illuminate\Support\Facades\DB;
use App\Contracts\LocationInterface;
use App\Traits\CanLoadRelationships;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\RestaurantRequest;
use App\Http\Resources\RestaurantResource;
use Illuminate\Support\Carbon as SupportCarbon;

class RestaurantController extends Controller
{
    use AddressTrait, CanLoadRelationships;

    private $restaurantInterface;
    private array $relations = [
        'ratings',
        'comments',
        'restaurantImages'
    ];

    public function __construct(LocationInterface $restaurantInterface)
    {
        $this->restaurantInterface = $restaurantInterface;
    }
    public function index()
    {
        try {
            $addressData = $this->restaurantInterface->relationData('Restaurant', 'address');
            return RestaurantResource::collection($addressData);
        } catch (\Exception $e) {
            return CrudException::emptyData();
        }
    }

    public function store(RestaurantRequest $request)
    {
        $validatedData = $request->validated();
        $validatedData['user_id'] = Auth::id();
        $addressData = Arr::pull($validatedData, 'addressData');
        $address = Address::firstOrCreate(
            [
                'street_id' => $addressData['street_id'],
                'block_no' => $addressData['block_no'],
                'floor' => $addressData['floor'],
            ],
            [
                'latitude' => $addressData['latitude'],
                'longitude' => $addressData['longitude'],
            ]
        );

        // Add the address ID to the main data
        $validatedData['address_id'] = $address->id;

        $validatedData = $this->dateFormat($validatedData);
        $restaurant = $this->restaurantInterface->store('Restaurant', $validatedData);
        if (!$restaurant) {
            return response()->json([
                'message' => Config::get('variable.RESTAURANT_NOT_FOUND')
            ], Config::get('variable.CLIENT_ERROR'));
        }
        return new RestaurantResource($restaurant);
    }

    // public function update(RestaurantRequest $restaurantRequest, string $id)
    // {
    //     // dd("OK");
    //     $validatedData = $restaurantRequest->validated();
    //     $validatedData = $this->dateFormat($validatedData);
    //     // dd($validatedData);
    //     $restaurant = $this->restaurantInterface->findById('Restaurant', $id);
    //     // dd($restaurant);
    //     if (!$restaurant) {

    //         return response()->json([
    //             'message' => Config::get('variable.RESTAURANT_NOT_FOUND')
    //         ], Config::get('variable.CLIENT_ERROR'));
    //     }
    //     $restaurant = $this->restaurantInterface->update('Restaurant', $validatedData, $id);
    //     // dd($restaurant);
    //     return new RestaurantResource($restaurant);
    // }

    // public function show(Restaurant $restaurant): RestaurantResource
    // {
    //     return new RestaurantResource($this->loadRelationships(Restaurant::where('id', $restaurant->id))->first());
    // }

    public function update(RestaurantRequest $restaurantRequest, string $id)
{
    $validatedData = $restaurantRequest->validated();
    $restaurant = $this->restaurantInterface->findById('Restaurant', $id);

    if (!$restaurant) {
        return response()->json([
            'message' => Config::get('variable.RESTAURANT_NOT_FOUND')
        ], Config::get('variable.CLIENT_ERROR'));
    }

    // Extract address data from validated request
    $addressData = Arr::pull($validatedData, 'addressData');

    if ($addressData) {
        // Find existing address or create a new one
        $address = Address::updateOrCreate(
            [
                'id' => $restaurant->address_id // Ensure it updates the correct address
            ],
            [
                'street_id' => $addressData['street_id'],
                'block_no' => $addressData['block_no'],
                'floor' => $addressData['floor'],
                'latitude' => $addressData['latitude'],
                'longitude' => $addressData['longitude'],
            ]
        );

        // Update the address_id in restaurant data
        $validatedData['address_id'] = $address->id;
    }

    // Format date fields
    $validatedData = $this->dateFormat($validatedData);

    // Update Restaurant
    $restaurant = $this->restaurantInterface->update('Restaurant', $validatedData, $id);

    return new RestaurantResource($restaurant);
}




    public function destroy(string $id)
    {
        $restaurant = $this->restaurantInterface->findById('Restaurant', $id);
        if (!$restaurant) {
            return response()->json([
                'message' => Config::get('variable.RESTAURANT_NOT_FOUND')
            ], Config::get('variable.CLIENT_ERROR'));
        }
        $this->restaurantInterface->delete('Restaurant', $id);
        return response()->json([
            'message' => Config::get('variable.RESTAURANT_DELETED_SUCCESSFULLY')
        ], Config::get('variable.NO_CONTENT'));
    }

    public function featureRestaurants()
    {
        $featureRestaurants = Restaurant::withCount('orderDetails as orders_count')
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get();

        return response()->json($featureRestaurants);
    }

    public function restaurantTypes()
    {
        // Correct the spelling of 'restaurant_types'
        $restaurantTypes = DB::table('restaurant_types')->get();

        // Return the correct variable
        return response()->json($restaurantTypes);
    }


}
