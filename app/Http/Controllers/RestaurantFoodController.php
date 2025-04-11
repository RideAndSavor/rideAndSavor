<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Images;
use App\Models\Topping;
use App\Models\Restaurant;

use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Models\FoodRestaurant;
use App\Services\ImageService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Contracts\LocationInterface;
use App\Http\Resources\FoodResource;
use Illuminate\Support\Facades\Config;
use function PHPUnit\Framework\isEmpty;
use App\Http\Requests\RestaurantFoodToppingRequest;
use Illuminate\Support\Facades\Storage;

class RestaurantFoodController extends Controller
{
    use ImageTrait;
    private $foodRestaurantInterface;
    private $genre;
    private $tableName;
    private $folder_name;
    protected $imageService;

    public function __construct(LocationInterface $locationInterface,ImageService $imageService)
    {
        $this->foodRestaurantInterface = $locationInterface;
        $this->genre = Config::get('variable.FOOD_IMAGE');
        $this->folder_name = 'public/foods/';
        $this->tableName = 'images';
        $this->imageService = $imageService;
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

    // public function storeFoodWithToppings1(RestaurantFoodToppingRequest $restaurantFoodToppingRequest, Restaurant $restaurant)
    // {

    //     // Validate the incoming request data
    //     $validatedData = $restaurantFoodToppingRequest->validated();
    //     // dd($validatedData);
    //     DB::beginTransaction(); // Begin the database transaction

    //     try {
    //         // Store the food details in the 'Food' table
    //         $food = $this->foodRestaurantInterface->store('Food', $validatedData['food']);
    //         // dd($food);

    //         // If there is an uploaded file, store the image
    //         if ($restaurantFoodToppingRequest->hasFile('upload_url')) {
    //             $this->storeImage($restaurantFoodToppingRequest, $food->id, $this->genre, $this->foodRestaurantInterface, $this->folder_name, $this->tableName);
    //         }

    //         // Store the toppings and get their IDs
    //         $toppingIDs = [];
    //         foreach ($validatedData['toppings'] as $toppingData) {
    //             $topping = $this->foodRestaurantInterface->store('Topping', $toppingData);
    //             $toppingIDs[] = $topping->id;
    //         }

    //         // Attach the toppings to the food
    //         $food->toppings()->attach($toppingIDs);

    //         // Attach the food to the restaurant with size and price details
    //         foreach ($validatedData['food_restaurant'] as $sizeData) {
    //             $restaurant->foods()->attach($food->id, [
    //                 'price' => $sizeData['price'],
    //                 'size_id' => $sizeData['size_id'],
    //                 'taste_id' => $validatedData['taste_id'],
    //                 'discount_item_id' => $validatedData['discount_item_id'] ?? null,
    //             ]);
    //         }

    //         DB::commit(); // Commit the transaction
    //         return new FoodResource($food); // Return the newly created food resource
    //     } catch (\Exception $e) {
    //         DB::rollBack(); // Rollback the transaction in case of an error
    //         return response()->json([
    //             'message' => Config::get('variable.FAIL_TO_CREATE_FOODTOPPING'), // Return an error message
    //             'error' => $e->getMessage() // Include the exception message for debugging
    //         ], Config::get('variable.SERVER_ERROR'));
    //     }
    // }

    // public function updateFoodTopping(
    //     RestaurantFoodToppingRequest $restaurantFoodToppingRequest,
    //     Restaurant $restaurant,
    //     Food $food
    // ) {
    //     $validatedData = $restaurantFoodToppingRequest->validated();
    //     DB::beginTransaction();

    //     try {
    //         // 1. Update Food Data
    //         if (isset($validatedData['food'])) {
    //             $foodData = [
    //                 'name' => $validatedData['food']['food_name'],
    //                 'sub_category_id' => $validatedData['food']['sub_category_id'] ?? null,
    //             ];
    //             $this->foodRestaurantInterface->update('Food', $foodData, $food->id);
    //         }

    //         // 2. Update Image (Replace Old Image)
    //         $existingImage = $food->images()->first();

    //         if ($restaurantFoodToppingRequest->hasFile('upload_url')) {
    //             $image = $restaurantFoodToppingRequest->file('upload_url');

    //             // Delete the old image file if it exists
    //             if ($existingImage) {
    //                 Storage::delete($existingImage->upload_url);
    //                 $existingImage->delete();
    //             }

    //             // Upload new image & store it
    //             $this->updateImageTest($food, [$image], 'food/');
    //         }

    //         // 3. Update Food & Restaurant Relationship
    //         $foodRestaurantData = [
    //             'size_id' => $validatedData['food_restaurant']['size_id'],
    //             'price' => $validatedData['food_restaurant']['price'],
    //             'description' => $validatedData['food_restaurant']['description'],
    //             'discount_item_id' => $validatedData['food_restaurant']['discount_item_id'] ?? null,
    //             'taste_id' => $validatedData['food_restaurant']['taste_id'] ?? null,
    //         ];

    //         // Update pivot table
    //         $food->restaurants()->updateExistingPivot($restaurant->id, $foodRestaurantData);

    //         // 4. Update Toppings (Replace Old with New)
    //         $newToppingIDs = [];
    //         if (!empty($validatedData['toppings'])) {
    //             foreach ($validatedData['toppings'] as $toppingData) {
    //                 if (!empty($toppingData['id'])) {
    //                     // Update existing topping
    //                     $this->foodRestaurantInterface->update('Topping', [
    //                         'name' => $toppingData['topping_name'],
    //                         'price' => $toppingData['topping_price']
    //                     ], $toppingData['id']);
    //                     $newToppingIDs[] = $toppingData['id'];
    //                 } else {
    //                     // Create new topping
    //                     $newTopping = $this->foodRestaurantInterface->store('Topping', [
    //                         'name' => $toppingData['topping_name'],
    //                         'price' => $toppingData['topping_price']
    //                     ]);
    //                     $newToppingIDs[] = $newTopping->id;
    //                 }
    //             }
    //         }
    //         $existingToppingIDs = $food->toppings()->pluck('toppings.id')->toArray();

    //         // 5. Remove Unused Toppings
    //         $toppingsToDetach = array_diff($existingToppingIDs, $newToppingIDs);
    //         $food->toppings()->detach($toppingsToDetach);

    //         // Sync New Toppings
    //         $food->toppings()->sync($newToppingIDs);

    //         DB::commit();
    //         return new FoodResource($food->fresh());
    //     } catch (\Exception $e) {
    //         DB::rollBack();
    //         return response()->json([
    //             'message' => 'Failed to update food with toppings',
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }





    public function destroyFoodTopping(Restaurant $restaurant, Food $food)
    {
        // dd("OK");
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
            // dd($imageData);

            // if ($imageData) {
            //     $this->deleteImage($this->foodRestaurantInterface, $imageData);
            // }

            // Ensure $imageData is not empty before deleting images
            if (!$imageData->isEmpty()) {
                foreach ($imageData as $image) {
                    $this->deleteImage($image->id); // Pass the image ID instead of the whole object
                }
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

    public function storeFoodWithToppings(RestaurantFoodToppingRequest $request)
    {
        $validatedData = $request->validated();
        // dd($validatedData);
        $image[] = $validatedData['upload_url'] ?? [];
        unset($validatedData['upload_url']);
        DB::beginTransaction();
        try {
            $foodData = [
                'name' => $validatedData['food']['food_name'],
                'sub_category_id' => $validatedData['food']['sub_category_id'], // Handle nullable sub_category_id
            ];
            $food = $this->foodRestaurantInterface->store('Food', $foodData);

            // if ($request->hasFile('upload_url')) {
            //     $this->storeImage($request, $food->id, $this->genre, $this->foodRestaurantInterface, $this->folder_name, $this->tableName);
            // }

            // dd($image);
            if (!empty($image)) {
                // dd($image);
                // dd($food);
                $this->createImageTest($food, $image, 'food/','food');
            }

            $foodRestaurantData = [
                'restaurant_id' => $validatedData['food_restaurant']['restaurant_id'],
                'size_id' => $validatedData['food_restaurant']['size_id'],
                'food_id' => $food->id,
                'discount_item_id' => $validatedData['food_restaurant']['discount_item_id'] ?? null,
                'price' => $validatedData['food_restaurant']['price'],
                'description' => $validatedData['food_restaurant']['description'],
                'taste_id' => $validatedData['food_restaurant']['taste_id'] ?? null,
            ];
            $foodRestaurant = $this->foodRestaurantInterface->store('FoodRestaurant', $foodRestaurantData);

            if ($request->has('toppings')) {
                foreach ($request->input('toppings') as $toppingData) {
                    $topping = $this->foodRestaurantInterface->store('Topping', [
                        'name' => $toppingData['topping_name'],
                        'price' => $toppingData['topping_price']
                    ]);
                    $food->toppings()->attach($topping->id);
                }
            }


            DB::commit();
            return response()->json([
                'message' => Config::get('variable.FOOD_RESTAURANT_AND_TOPPING_CREATE_SUCCESSFULLY')
            ], Config::get('variable.OK'));
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => Config::get('variable.FAIL TO CREATE'),
                'error' => $e->getMessage()
            ], Config::get('variable.CLIENT_ERROR'));
        }
    }

    // public function update(RestaurantFoodToppingRequest $request, $foodRestaurantId)
    // {
    //     $folder_name = 'public/foods/';
    //     $tableName = 'images';
    //     $validatedData = $request->validated();
    //     DB::beginTransaction();

    //     try {
    //         $foodRestaurant = $this->foodRestaurantInterface->findById('FoodRestaurant', $foodRestaurantId);
    //         $imageData = $this->foodRestaurantInterface->findWhere('Images', $foodRestaurant->food_id);
    //         if (!$foodRestaurant) {
    //             return response()->json(['message' => 'FoodRestaurant record not found!'], 404);
    //         }

    //         $foodRestaurantData = [
    //             'restaurant_id' => $validatedData['food_restaurant']['restaurant_id'],
    //             'size_id' => $validatedData['food_restaurant']['size_id'],
    //             'price' => $validatedData['food_restaurant']['price'],
    //             'description' => $validatedData['food_restaurant']['description'],
    //             'taste_id' => $validatedData['food_restaurant']['taste_id'] ?? null,
    //             'discount_item_id' => $validatedData['food_restaurant']['discount_item_id'] ?? null,
    //         ];
    //         $this->foodRestaurantInterface->update('FoodRestaurant', $foodRestaurantData, $foodRestaurant->id);
    //         // Update food data
    //         if ($validatedData['food']) {
    //             $foodData = [
    //                 'name' => $validatedData['food']['food_name'],
    //                 'sub_category_id' => $validatedData['food']['sub_category_id'],
    //             ];
    //             $this->foodRestaurantInterface->update('Food', $foodData, $foodRestaurant->food_id);
    //             $foodId = $foodRestaurant->food_id;
    //         }
    //         if ($request->hasFile('upload_url')) {
    //             $this->updateImage($request, $imageData, $foodId, $this->genre, $this->foodRestaurantInterface, $folder_name, $tableName, $foodRestaurantId);
    //         }

    //         $food = $foodRestaurant->food;
    //         $existingToppings = $food->toppings->keyBy('id');
    //         $existingToppingIDs = $existingToppings->pluck('id')->toArray();
    //         $newToppingIDs = [];
    //         if ($request->has('toppings')) {
    //             foreach ($request->input('toppings') as $toppingData) {
    //                 if (isset($toppingData['id'])) {
    //                     $this->foodRestaurantInterface->update('Topping', [
    //                         'name' => $toppingData['topping_name'],
    //                         'price' => $toppingData['topping_price']
    //                     ], $toppingData['id']);
    //                     $newToppingIDs[] = $toppingData['id'];
    //                 } else {
    //                     $newTopping = $this->foodRestaurantInterface->store('Topping', [
    //                         'name' => $toppingData['topping_name'],
    //                         'price' => $toppingData['topping_price']
    //                     ]);
    //                     $newToppingIDs[] = $newTopping->id;
    //                     $food->toppings()->attach($newTopping->id);
    //                 }
    //             }

    //         }

    //         $extraToppingIDs = array_diff($existingToppingIDs, $newToppingIDs);
    //         if (count($extraToppingIDs) > 0) {
    //             foreach ($extraToppingIDs as $extraToppingID) {
    //                 $this->foodRestaurantInterface->delete('Topping', $extraToppingID);
    //                 $food->toppings()->detach($extraToppingID);
    //             }
    //         }


    //         DB::commit();

    //         return response()->json([
    //             'message' => Config::get('variable.FOOD_RESTAURANT_AND_TOPPING_UPDATE_SUCCESSFULLY')
    //         ], Config::get('variable.OK'));

    //     } catch (\Exception $e) {
    //         DB::rollBack();

    //         return response()->json([
    //             'message' => Config::get('variable.FAIL_TO_UPDATE'),
    //             'error' => $e->getMessage(),
    //         ], Config::get('variable.CLIENT_ERROR'));
    //     }
    // }

    public function destroy($id, Request $request)
    {
        $type = $request->input('type');
        DB::beginTransaction();
        try {
            if ($type === 'food') {
                $this->deleteFood($id);
            } elseif ($type === 'restaurant') {
                $this->deleteFoodRestaurant($id);
            } else {
                return response()->json(['message' => 'Invalid type specified.'], 400);
            }

            DB::commit();
            return response()->json(['message' => 'Deleted successfully.'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Delete operation failed.', 'error' => $e->getMessage()], 500);
        }
    }


    private function deleteFood($foodId)
    {
        $this->foodRestaurantInterface->delete('Food', $foodId);
        DB::commit();
        return response()->json([
            'message' => Config::get('variable.DELETE_SUCCESSFULLY')
        ], Config::get('variable.OK'));
    }

    private function deleteFoodRestaurant($foodRestaurantId)
    {
        $this->foodRestaurantInterface->delete('FoodRestaurant', $foodRestaurantId);
        DB::commit();
        return response()->json([
            'message' => Config::get('variable.DELETE_SUCCESSFULLY')
        ], Config::get('variable.OK'));
    }


}
