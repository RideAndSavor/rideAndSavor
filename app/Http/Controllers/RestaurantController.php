<?php

namespace App\Http\Controllers;

use App\Contracts\LocationInterface;
use App\Exceptions\CrudException;
use App\Http\Requests\RestaurantRequest;
use App\Http\Resources\RestaurantResource;
use App\Models\Restaurant;
use App\Traits\AddressTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon as SupportCarbon;
use Illuminate\Support\Facades\Config;

class RestaurantController extends Controller
{
    use AddressTrait;

    private $restaurantInterface;

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
        $validatedData = $this->dateFormat($validatedData);
        $restaurant = $this->restaurantInterface->store('Restaurant', $validatedData);
        if (!$restaurant) {
            return response()->json([
                'message' => Config::get('variable.RESTAURANT_NOT_FOUND')
            ], Config::get('variable.CLIENT_ERROR'));
        }
        return new RestaurantResource($restaurant);
    }

    public function update(RestaurantRequest $restaurantRequest, string $id)
    {
        $validatedData = $restaurantRequest->validated();
        $validatedData =  $this->dateFormat($validatedData);
        $restaurant = $this->restaurantInterface->findById('Restaurant', $id);
        if (!$restaurant) {
            return response()->json([
                'message' => Config::get('variable.RESTAURANT_NOT_FOUND')
            ], Config::get('variable.CLIENT_ERROR'));
        }
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
}
