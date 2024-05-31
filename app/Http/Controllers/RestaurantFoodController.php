<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use App\Contracts\LocationInterface;
use Illuminate\Support\Facades\Config;

use function PHPUnit\Framework\isEmpty;
use App\Http\Requests\RestaurantFoodIngredientRequest;
use App\Http\Resources\FoodResource;
use App\Models\Food;
use App\Traits\ImageTrait;

class RestaurantFoodController extends Controller
{
    use ImageTrait;
    private $foodRestaurantInterface;
    private $genre;
    private $tableName;
    private $folder_name;

    public function __construct(LocationInterface $locationInterface)
    {
        $this->foodRestaurantInterface = $locationInterface;
        $this->genre = Config::get('variable.FOOD_IMAGE');
        $this->folder_name = 'public/foods/';
        $this->tableName = 'images';
    }

    public function showAllFoodIngredients(Restaurant $restaurant)
    {
        $foods = $restaurant->foods()->with('ingredients')->get();

        $uniqueFoods = $foods->unique('id'); // Food id is duplicate that why we need to do unique for food_id

        return FoodResource::collection($uniqueFoods);
    }
 
    public function showFoodIngredient(Restaurant $restaurant, Food $food)
    {
        $relatedFood = $restaurant->foods()->wherePivot('food_id', $food->id)
            ->first();
        return new FoodResource($relatedFood);
    }

    public function storeFoodWithIngredients(RestaurantFoodIngredientRequest $restaurantFoodIngredientRequest, Restaurant $restaurant)
    {
        $validatedData = $restaurantFoodIngredientRequest->validated();
        DB::beginTransaction();

        try {
            $food = $this->foodRestaurantInterface->store('Food', $validatedData['food']);

            if ($restaurantFoodIngredientRequest->hasFile('upload_url')) {
                $this->storeImage($restaurantFoodIngredientRequest, $food->id, $this->genre, $this->foodRestaurantInterface, $this->folder_name, $this->tableName);
            }

            $ingredinetIDs = [];
            foreach ($validatedData['ingredients'] as $ingredinetData) {
                $ingredinet = $this->foodRestaurantInterface->store('Ingredient', $ingredinetData);
                $ingredinetIDs[] = $ingredinet->id;
            }
            $food->ingredients()->attach($ingredinetIDs);

            foreach ($validatedData['food_restaurant'] as $sizeData) {
                $restaurant->foods()->attach($food->id, [
                    'price' => $sizeData['price'],
                    'size_id' => $sizeData['size_id'],
                    'discount_item_id' => $validatedData['discount_item_id'] ?? null,

                ]);
            }
            DB::commit();
            return new FoodResource($food);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => Config::get('variable.FAIL_TO_CREATE_FOODINGREDIENT')
            ], Config::get('variable.SEVER_ERROR'));
        }
    }

    public function updateFoodIngredient(
        RestaurantFoodIngredientRequest $restaurantFoodIngredientRequest,
        Restaurant $restaurant,
        Food $food
    ) {
        $validatedData = $restaurantFoodIngredientRequest->validated();

        $this->foodRestaurantInterface->update('Food', $validatedData['food'], $food->id);

        $existingIngredients = $food->ingredients;
        $existingIngredientIDs = $existingIngredients->pluck('id')->toArray();

        foreach ($validatedData['ingredients'] as $index => $ingredientData) {
            if (isset($existingIngredients[$index])) {
                // Update the existing ingredient
                $ingredient = $this->foodRestaurantInterface->update('Ingredient', $ingredientData, $existingIngredients[$index]->id);
                $newIngredientIDs[] = $ingredient->id;
            } else {
                // Add a new ingredient if there are more validated ingredients than existing ones
                $newIngredient = $this->foodRestaurantInterface->store('Ingredient', $ingredientData);
                $newIngredientIDs[] = $newIngredient->id;
            }
        }


        // Remove excess ingredients

        // $food->ingredients()->detach($ingredientsToRemove);


        // Step 3: Detach ingredients that are no longer associated
        // $ingredientsToRemove = array_diff($existingIngredientIDs, $newIngredientIDs);
        // if (!empty($ingredientsToRemove)) {
        //     $food->ingredients()->detach($ingredientsToRemove);
        // }
    }
}
