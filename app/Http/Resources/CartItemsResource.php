<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'foods' => new FoodResource($this->whenLoaded('food')),
            'restaurant' => new RestaurantResource($this->whenLoaded('restaurant')),
        ];
    }
}