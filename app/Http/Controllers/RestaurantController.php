<?php

namespace App\Http\Controllers;

use App\Contracts\LocationInterface;
use App\Http\Requests\RestaurantRequest;
use App\Http\Resources\RestaurantResource;
use App\Models\Address;
use App\Models\Street;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class RestaurantController extends Controller
{

    private $restaurantInterface;

    public function __construct(LocationInterface $restaurantInterface) {
        $this->restaurantInterface = $restaurantInterface;
    }
    public function index()
    {
        //
    }

    public function store(RestaurantRequest $request)
    {
        $validateData = $request->validated();
        // $address = Address::findOrFail($validateData['address_id']);
        // dd($address->latitude);
        $restaurant = $this->restaurantInterface->store('Restaurant',$validateData);
        if(!$restaurant){
            return response()->json([
                'message'=>Config::get('variable.RESTAURANT_NOT_FOUND')
            ],Config::get('variable.CLIENT_ERROR'));
        }
        return new RestaurantResource($restaurant);
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
