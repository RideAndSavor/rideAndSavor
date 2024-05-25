<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodRestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'restaurant_id'=>$this->restaurant_id,
            'food_id'=>$this->food_id,
            'price'=>$this->price,
            'size_id'=>$this->size_id,
            'discount_item_id'=>$this->discount_item_id,
        ];
    }

    public function with(Request $request)
    {
        return [
            'version' => '1.0.0',
            'api_url' => url('http://127.0.0.1:8000/api/foodRestaurant'),
            'message' => 'Your action is successful'
        ];
    }
}
