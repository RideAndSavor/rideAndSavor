<?php

namespace App\Http\Controllers;

use App\Contracts\LocationInterface;
use App\Http\Resources\DiscountFoodResource;
use App\Http\Resources\FoodResource;
use App\Models\FoodRestaurant;
use App\Models\Percentage;
use Illuminate\Http\Request;

class DiscountController extends Controller
{
    private $discountInterface;
    public function __construct(LocationInterface $locationInterface)
    {
        $this->discountInterface = $locationInterface;
    }

    public function getDiscountFoods()
    {
        $foodsDatas = [];
        $foodRestaurantDatas = FoodRestaurant::whereNotNull('discount_item_id')->get();
        $uniqueFoodRestauranatDatas = $foodRestaurantDatas->unique('food_id');

        foreach ($uniqueFoodRestauranatDatas as $uniqueFoodRestaurantData) {
            $foodData = $this->discountInterface->findById('Food', $uniqueFoodRestaurantData->food_id);
            $restaurantData = $this->discountInterface->findById('Restaurant', $uniqueFoodRestaurantData->restaurant_id);
            $discountItemData = $this->discountInterface->findById('DiscountItem', $uniqueFoodRestaurantData->discount_item_id);
            $percentageData = $this->discountInterface->findById('Percentage', $discountItemData->percentage_id);
            $discountedPrice = $percentageData->discount($uniqueFoodRestaurantData->price, $percentageData->discount_percentage);

            $foodsDatas[] = [
                'name' => $foodData->name,
                'restaurant_name' => $restaurantData->name,
                'original_price' => $uniqueFoodRestaurantData->price,
                'discounted_price' => $discountedPrice,
                'discount_promotion_name' => $discountItemData->name,
                'discount_percentage' => $percentageData->discount_percentage,
                'start_Date' => $discountItemData->start_date,
                'end_Date' => $discountItemData->end_date
            ];
        }
        return DiscountFoodResource::collection($foodsDatas);
    }
}
