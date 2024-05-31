<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Address;
use Cron\DayOfWeekField;
use Illuminate\Http\Request;
use App\Helpers\DistanceHelper;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

class CalculateDeliveryFeesController extends Controller
{
    public function calculateDeliveryFee(Request $request)
    {
        try {
            // Find the user and restaurant address
            $user = User::findOrFail($request->user_id);
            $restaurantAddress = Address::findOrFail($request->restaurant_address_id);

            // Get the user's first address
            $userAddress = $user->addresses->first();

            // Check if user's address and the necessary coordinates are available
            if ($userAddress && $userAddress->latitude !== null && $userAddress->longitude !== null &&
                $restaurantAddress->latitude !== null && $restaurantAddress->longitude !== null) {

                // Calculate the distance
                $distance = DistanceHelper::calculateDistance(
                    $userAddress->latitude,
                    $userAddress->longitude,
                    $restaurantAddress->latitude,
                    $restaurantAddress->longitude
                );

                $rateTiers = [
                    'default'=>1000,
                    'peak_hour'=>1200,
                    'weekends'=>1500
                ];

                $currentRate = $rateTiers['default'];

                $currentHour = now()->hour;
                if( $currentHour >= 18 && $currentHour <= 21){
                    $currentRate = $rateTiers['peak_hour'];
                }

                $currentDayOfWeek = now()->dayOfWeek;
                if($currentDayOfWeek == 6 || $currentDayOfWeek == 0){ //6 = Saturday    0 =Sunday
                    $currentRate = $rateTiers['weekends'];
                }

                // Calculate the delivery fee
                $deliveryFee = $distance * $currentRate;

                // Return the response
                return response()->json([
                    'distance' => $distance,
                    'delivery_fee' => $deliveryFee
                ]);
            } else {
                return response()->json([
                    'error' => 'Latitude and longitude for user or restaurant address are not available.'
                ], 400);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An error occurred: ' . $e->getMessage()
            ], Config::get('variable.INTERNAL_SEVER_ERROR'));
        }
    }
}
