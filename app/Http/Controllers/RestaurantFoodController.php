<?php

namespace App\Http\Controllers;

use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;
use App\Contracts\LocationInterface;
use Illuminate\Support\Facades\Config;

use function PHPUnit\Framework\isEmpty;
use App\Http\Requests\RestaurantFoodToppingRequest;
use App\Http\Resources\FoodResource;
use App\Models\Food;
use App\Models\FoodRestaurant;
use App\Models\Images;
use App\Models\Topping;
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

    public function showAllFoodToppings(Restaurant $restaurant)
    {
        $foods = $restaurant->foods()->with('toppings')->get();

        $uniqueFoods = $foods->unique('id'); // Food id is duplicate that why we need to do unique for food_id

        return FoodResource::collection($uniqueFoods);
    }

    public function showFoodTopping(Restaurant $restaurant, Food $food)
    {
        $relatedFood = $restaurant->foods()->wherePivot('food_id', $food->id)
            ->first();
        return new FoodResource($relatedFood);
    }

    public function storeFoodWithToppings(RestaurantFoodToppingRequest $restaurantFoodToppingRequest, Restaurant $restaurant)
    {
        // Validate the incoming request data
        $validatedData = $restaurantFoodToppingRequest->validated();
        DB::beginTransaction(); // Begin the database transaction

        try {
            // Store the food details in the 'Food' table
            $food = $this->foodRestaurantInterface->store('Food', $validatedData['food']);

            // If there is an uploaded file, store the image
            if ($restaurantFoodToppingRequest->hasFile('upload_url')) {
                $this->storeImage($restaurantFoodToppingRequest, $food->id, $this->genre, $this->foodRestaurantInterface, $this->folder_name, $this->tableName);
            }

            // Store the toppings and get their IDs
            $toppingIDs = [];
            foreach ($validatedData['toppings'] as $toppingData) {
                $topping = $this->foodRestaurantInterface->store('Topping', $toppingData);
                $toppingIDs[] = $topping->id;
            }

            // Attach the toppings to the food
            $food->toppings()->attach($toppingIDs);

            // Attach the food to the restaurant with size and price details
            foreach ($validatedData['food_restaurant'] as $sizeData) {
                $restaurant->foods()->attach($food->id, [
                    'price' => $sizeData['price'],
                    'size_id' => $sizeData['size_id'],
                    'taste_id' => $validatedData['taste_id'],
                    'discount_item_id' => $validatedData['discount_item_id'] ?? null,
                ]);
            }

            DB::commit(); // Commit the transaction
            return new FoodResource($food); // Return the newly created food resource
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback the transaction in case of an error
            return response()->json([
                'message' => Config::get('variable.FAIL_TO_CREATE_FOODTOPPING'), // Return an error message
                'error' => $e->getMessage() // Include the exception message for debugging
            ], Config::get('variable.SERVER_ERROR'));
        }
    }


    public function updateFoodTopping(
        RestaurantFoodToppingRequest $restaurantFoodToppingRequest,
        Restaurant $restaurant,
        Food $food
    ) {
        $validatedData = $restaurantFoodToppingRequest->validated();
        DB::beginTransaction();

        try {
            // Update the food details
            $this->foodRestaurantInterface->update('Food', $validatedData['food'], $food->id);

            // Handle the image update if there's a file uploaded
            $imageData = $this->foodRestaurantInterface->findWhere('Images', $food->id);
            if (!$imageData) {
                return response()->json([
                    'message' => Config::get('variable.IMAGE_DATA_NOT_FOUND')
                ], Config::get('variable.CLIENT_ERROR'));
            }

            if ($restaurantFoodToppingRequest->hasFile('upload_url')) {
                $this->updateImage($restaurantFoodToppingRequest, $imageData, $food->id, $this->genre, $this->foodRestaurantInterface, $this->folder_name, $this->tableName);
            }

            // Update existing toppings and add new ones
            $existingToppings = $food->toppings;
            $existingToppingIDs = $existingToppings->pluck('id')->toArray();
            $newToppingIDs = [];

            foreach ($validatedData['toppings'] as $index => $toppingData) {
                if (isset($existingToppings[$index])) {
                    $this->foodRestaurantInterface->update('Topping', $toppingData, $existingToppings[$index]->id);
                    $newToppingIDs[] = $existingToppings[$index]->id;
                } else {
                    $newTopping = $this->foodRestaurantInterface->store('Topping', $toppingData);
                    $newToppingIDs[] = $newTopping->id;
                }
            }

            // Delete any extra toppings that were removed
            if (count($existingToppingIDs) > count($validatedData['toppings'])) {
                $extraToppingIDs = array_slice($existingToppingIDs, count($validatedData['toppings']));
                foreach ($extraToppingIDs as $extraToppingID) {
                    $this->foodRestaurantInterface->delete('Topping', $extraToppingID);
                }
                $food->toppings()->detach($extraToppingIDs);
            }

            // Sync the new toppings IDs
            $food->toppings()->sync($newToppingIDs);

            // Sync the food and restaurant relationships
            $food->restaurants()->sync($this->mapFoodRestaurant($food->id, $restaurant->id, $validatedData['food_restaurant'], $validatedData['discount_item_id']));

            DB::commit();
            return new FoodResource($food);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => Config::get('variable.FAIL_TO_UPDATE_FOODTOPPING'),
                'error' => $e->getMessage()
            ], Config::get('variable.SEVER_ERROR'));
        }
    }

    public function destroyFoodTopping(Restaurant $restaurant, Food $food)
    {
        DB::beginTransaction();

        try {
            // Get all topping IDs associated with the food
            $toppingIDs = $food->toppings()->pluck('topping_id');
            // dd($toppingIDs);
            // Use each for side effects
            $toppingIDs->each(function ($toppingID) {
                $this->foodRestaurantInterface->delete('topping', $toppingID);
            });

            // Detach the toppings associated with the food
            $food->toppings()->detach();

            // Delete the food record
            $this->foodRestaurantInterface->delete('Food', $food->id);

            $imageData = $this->foodRestaurantInterface->findWhere('Images', $food->id);
            if ($imageData) {
                $this->deleteImage($this->foodRestaurantInterface, $imageData);
            }

            // Delete the food entry from the pivot table with the restaurant
            $restaurant->foods()->detach($food->id);

            DB::commit();
            return response()->json([
                'message' => Config::get('variable.FOOD AND TOPPINGS SUCCESSFULLY DELETED')
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
                'description' => $food['description'],
                'taste_id' => $taste_id ?? null
            ];
        })->toArray();
    }

    public function store(RestaurantFoodToppingRequest $request)
    {
        DB::beginTransaction();
        try {
            $food = Food::create([
                'name' => $request->input('food.food_name'),
                'sub_category_id' => $request->input('food.sub_category_id')
            ]);
            $restaurantFood = FoodRestaurant::create([
                'restaurant_id' => $request->input('food_restaurant.restaurant_id'),
                'size_id' => $request->input('food_restaurant.size_id'),
                'food_id' => $food->id,
                'discount_item_id' => $request->input('food_restaurant.discount_item_id') ?? null,
                'price' => $request->input('food_restaurant.price'),
                'description' => $request->input('food_restaurant.description'),
                'taste_id' => $request->input('food_restaurant.taste_id') ?? null
            ]);
            if($request->has('toppings'))
            {
                foreach($request->input('toppings') as $toppingData)
                {
                    $topping = Topping::create([
                        'name' => $toppingData['topping_name'],
                        'price' => $toppingData['topping_price']
                    ]);
                    $food->toppings()->attach($topping->id);
                }
            }
            DB::commit();
            return response()->json(['message'=>'Data successfully stored'], 201);
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Data storing failed!', 'error' => $e->getMessage()], 500);
        }
    }
}
