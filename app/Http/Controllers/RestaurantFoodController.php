<?php

namespace App\Http\Controllers;

use App\Contracts\LocationInterface;
use App\Http\Requests\RestaurantFoodIngredientRequest;
use App\Models\Restaurant;
use Illuminate\Support\Facades\Config;

use function PHPUnit\Framework\isEmpty;

class RestaurantFoodController extends Controller
{
    private $foodRestaurantInterface;

    public function __construct(LocationInterface $locationInterface)
    {
        $this->foodRestaurantInterface = $locationInterface;
    }

    public function storeFoodWithIngredients(RestaurantFoodIngredientRequest $restaurantFoodIngredientRequest, Restaurant $restaurant)
    {
        $validatedData = $restaurantFoodIngredientRequest->validated();
        $food = $this->foodRestaurantInterface->store('Food', $validatedData['food']);

        $ingredinetIDs = [];
        foreach ($validatedData['ingredients'] as $ingredinetData) {
            $ingredinet = $this->foodRestaurantInterface->store('Ingredient', $ingredinetData);
            $ingredinetIDs[] = $ingredinet->id;
        }
    }
}
