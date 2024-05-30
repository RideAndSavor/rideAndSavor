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
use App\Models\FoodRestaurant;
use App\Models\Images;
use App\Traits\ImageTrait;
use Illuminate\Support\Facades\Log;

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
        // Validate the incoming request data
        $validatedData = $restaurantFoodIngredientRequest->validated();
        DB::beginTransaction(); // Begin the database transaction

        try {
            // Store the food details in the 'Food' table
            $food = $this->foodRestaurantInterface->store('Food', $validatedData['food']);

            // If there is an uploaded file, store the image
            if ($restaurantFoodIngredientRequest->hasFile('upload_url')) {
                $this->storeImage($restaurantFoodIngredientRequest, $food->id, $this->genre, $this->foodRestaurantInterface, $this->folder_name, $this->tableName);
            }

            // Store the ingredients and get their IDs
            $ingredinetIDs = [];
            foreach ($validatedData['ingredients'] as $ingredinetData) {
                $ingredinet = $this->foodRestaurantInterface->store('Ingredient', $ingredinetData);
                $ingredinetIDs[] = $ingredinet->id;
            }

            // Attach the ingredients to the food
            $food->ingredients()->attach($ingredinetIDs);

            // Attach the food to the restaurant with size and price details
            foreach ($validatedData['food_restaurant'] as $sizeData) {
                $restaurant->foods()->attach($food->id, [
                    'price' => $sizeData['price'],
                    'size_id' => $sizeData['size_id'],
                    'discount_item_id' => $validatedData['discount_item_id'] ?? null,
                ]);
            }

            DB::commit(); // Commit the transaction
            return new FoodResource($food); // Return the newly created food resource
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction in case of an error
            return response()->json([
                'message' => Config::get('variable.FAIL_TO_CREATE_FOODINGREDIENT'), // Return an error message
                'error' => $e->getMessage() // Include the exception message for debugging
            ], Config::get('variable.SERVER_ERROR'));
        }
    }


    public function updateFoodIngredient(
        RestaurantFoodIngredientRequest $restaurantFoodIngredientRequest,
        Restaurant $restaurant,
        Food $food
    ) {
        $validatedData = $restaurantFoodIngredientRequest->validated();
        DB::beginTransaction();

        try {
            // Update the food details
            $this->foodRestaurantInterface->update('Food', $validatedData['food'], $food->id);

            // Handle the image update if there's a file uploaded
            $iamgeDatas = $this->foodRestaurantInterface->findWhere('Images', $food->id);
            if (!$iamgeDatas) {
                return response()->json([
                    'message' => Config::get('variable.IMAGE_DATA_NOT_FOUND')
                ], Config::get('variable.CLIENT_ERROR'));
            }

            if ($restaurantFoodIngredientRequest->hasFile('upload_url')) {
                $this->updateImage($restaurantFoodIngredientRequest, $iamgeDatas, $food->id, $this->genre, $this->foodRestaurantInterface, $this->folder_name, $this->tableName);
            }

            // Update existing ingredients and add new ones
            $existingIngredients = $food->ingredients;
            $existingIngredientIDs = $existingIngredients->pluck('id')->toArray();
            $newIngredientIDs = [];

            foreach ($validatedData['ingredients'] as $index => $ingredientData) {
                if (isset($existingIngredients[$index])) {
                    $this->foodRestaurantInterface->update('Ingredient', $ingredientData, $existingIngredients[$index]->id);
                    $newIngredientIDs[] = $existingIngredients[$index]->id;
                } else {
                    $newIngredient = $this->foodRestaurantInterface->store('Ingredient', $ingredientData);
                    $newIngredientIDs[] = $newIngredient->id;
                }
            }

            // Delete any extra ingredients that were removed
            if (count($existingIngredientIDs) > count($validatedData['ingredients'])) {
                $extraIngredientIDs = array_slice($existingIngredientIDs, count($validatedData['ingredients']));
                foreach ($extraIngredientIDs as $extraIngredientID) {
                    $this->foodRestaurantInterface->delete('Ingredient', $extraIngredientID);
                }
                $food->ingredients()->detach($extraIngredientIDs);
            }

            // Sync the new ingredient IDs
            $food->ingredients()->sync($newIngredientIDs);

            // Sync the food and restaurant relationships
            $food->restaurants()->sync($this->mapFoodRestaurant($food->id, $restaurant->id, $validatedData['food_restaurant'], $validatedData['discount_item_id']));

            DB::commit();
            return new FoodResource($food);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => Config::get('variable.FAIL_TO_UPDATE_FOODINGREDIENT'),
                'error' => $e->getMessage()
            ], Config::get('variable.SEVER_ERROR'));
        }
    }

    public function destroyFoodIngredient(Restaurant $restaurant, Food $food)
    {
        DB::beginTransaction();

        try {
            // Get all ingredient IDs associated with the food
            $ingredientIDs = $food->ingredients()->pluck('ingredient_id');
            // dd($ingredientIDs);
            // Use each for side effects
            $ingredientIDs->each(function ($ingredientID) {
                $this->foodRestaurantInterface->delete('Ingredient', $ingredientID);
            });

            // Detach the ingredients associated with the food
            $food->ingredients()->detach();

            // Delete the food record
            $this->foodRestaurantInterface->delete('Food', $food->id);

            $iamgeDatas = $this->foodRestaurantInterface->findWhere('Images', $food->id);
            if ($iamgeDatas) {
                $this->deleteImage($this->foodRestaurantInterface, $iamgeDatas);
            }

            // Delete the food entry from the pivot table with the restaurant
            $restaurant->foods()->detach($food->id);

            DB::commit();
            return response()->json([
                'message' => Config::get('variable.FOOD AND INGREDIENTS SUCCESSFULLY DELETED')
            ], Config::get('variable.OK'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => Config::get('variable.FAIL TO DELETE FOOD AND INREDIENTS'),
                'error' => $e->getMessage()
            ], Config::get('variable.CLIENT_ERROR')); // Ensure the status code is an integer
        }
    }


    private function mapFoodRestaurant($food_id, $restaurant_id, $foods_restaurant_price, $discount_item_id)
    {
        return collect($foods_restaurant_price)->map(function ($food) use ($discount_item_id, $food_id, $restaurant_id) {
            return [
                'food_id' => $food_id,
                'restaurant_id' => $restaurant_id,
                'size_id' => $food['size_id'],
                'price' => $food['price'],
                'discount_item_id' => $discount_item_id ?? null,
            ];
        })->toArray();
    }
}
