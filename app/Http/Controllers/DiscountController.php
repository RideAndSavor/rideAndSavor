<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Models\Percentage;
use App\Models\DiscountItem;
use Illuminate\Http\Request;
use App\Models\FoodRestaurant;
use Illuminate\Support\Facades\DB;
use App\Contracts\LocationInterface;
use App\Http\Resources\FoodResource;
use App\Http\Requests\DiscountItemRequest;
use App\Http\Resources\DiscountFoodResource;

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

    public function storeForShop(DiscountItemRequest $request)
    {
        $validated = $request->validated();

        DB::beginTransaction();

        try {
            // Validate shop exists
            $shop = Shop::findOrFail($request->shop_id);

            // Create DiscountItem
            $discount = DiscountItem::create([
                'percentage_id' => $validated['percentage_id'],
                'name' => $validated['name'],
                'start_date' => $validated['start_date'],
                'end_date' => $validated['end_date'],
            ]);

            // Assign discount to shop
            $shop->discount_id = $discount->id;
            $shop->save();

            DB::commit();

            return response()->json([
                'message' => 'Discount created and assigned to shop successfully.',
                'discount' => $discount,
                'shop' => $shop
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Failed to create discount for shop.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
