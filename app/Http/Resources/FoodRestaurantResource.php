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
            'id' => $this->id,
            'price' => $this->price,
            'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),
            'food' => new FoodResource($this->whenLoaded('food')),
            'size' => new SizeResource($this->whenLoaded('size')),
            'discount' => new DiscountItemResource($this->whenLoaded('discount')),
        ];
    }

    public function with(Request $request)
    {
        return [
            'version' => '1.0.0',
            'api_url' => url('http://api.dailyfairdeal.com/api/foodRestaurant'),
            'message' => 'Your action is successful'
        ];
    }
}
