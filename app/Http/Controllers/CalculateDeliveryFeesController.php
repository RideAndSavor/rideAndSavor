<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use App\Helpers\DistanceHelper;
use Illuminate\Support\Facades\Auth;

class CalculateDeliveryFeesController extends Controller
{
    public function calculateDeliveryFee(Request $request){
        $user = User::find($request->user_id);
        $restaurantAddress = Address::find($request->restaurant_address_id);

        $userAddress = $user->addresses->first();
         if($userAddress){
            $distance = DistanceHelper::calculateDistance(
                $userAddress->latitude,
                $userAddress->longitude,
                $restaurantAddress->latitude,
                $restaurantAddress->longitude
            );

            $deliveryFee = $distance * 1000 ; //1 km = 1000 MMK
            return response()->json([
                'distance'=>$distance,
                'delivery_fee'=>$deliveryFee
            ]);
         }else{
            return response()->json([
                'error'=>'User address Not Found'
            ],404);
         }

    }
}
