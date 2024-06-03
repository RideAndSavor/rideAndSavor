<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'order_id'=>$this->order_id,
            'food_restaurant_id'=>$this->food_restaurant_id,
            'quantity'=>$this->quantity,
            'discount_prices'=>$this->discount_prices,
        ];    
    }

    public function with(Request $request)
    {
        return[
            'version' => '1.0.0',
            'api_url' => url('http://127.0.0.1:8000/api/orderDetail'),
            'message' => 'Your action is successful'
        ];
    }
}
