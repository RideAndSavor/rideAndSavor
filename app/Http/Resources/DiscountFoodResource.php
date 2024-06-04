<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountFoodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this['name'],
            'restaurant_name' => $this['restaurant_name'],
            'original_price' => $this['original_price'],
            'discounted_price' => $this['discounted_price'],
            'discount_promotion_name' => $this['discount_promotion_name'],
            'discount_percentage' => $this['discount_percentage'],
            'start_Date' => $this['start_Date'],
            'end_Date' => $this['end_Date']
        ];
    }
}
