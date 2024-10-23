<?php

namespace App\Http\Controllers;
use App\Models\Food;
use Illuminate\Http\Request;
use App\Models\FoodRestaurant;
use App\Http\Resources\FoodResource;

class FoodsInRestaurant extends Controller
{
    public function __invoke(Request $request)
    {
        $request->validate([
            'restaurant_id' => ['required', 'integer']
        ]);
        $foodIDs = FoodRestaurant::where('restaurant_id', $request->input('restaurant_id'))->pluck('food_id');
        $foodsInRestaurant = Food::query()->whereIn('id', $foodIDs)
            ->with(['subCategory.category'])->get();
        return FoodResource::collection($foodsInRestaurant);
    }
}
