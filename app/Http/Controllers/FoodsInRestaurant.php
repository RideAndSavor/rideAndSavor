<?php

namespace App\Http\Controllers;
use App\Models\Food;
use Illuminate\Http\Request;
use App\Models\FoodRestaurant;
use App\Http\Resources\FoodResource;
use App\Http\Resources\FoodRestaurantResource;

class FoodsInRestaurant extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'restaurant_id' => ['required', 'integer']
        ]);

        $foodsInRestaurant = FoodRestaurant::where('restaurant_id', $request->input('restaurant_id'))
            ->with(['restaurant', 'food.subCategory.category', 'size', 'discount.percentage'])->first();
        return new FoodRestaurantResource($foodsInRestaurant);
    }
}
