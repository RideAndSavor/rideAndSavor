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
            'order_id' => $this->order_id,
            // 'restaurant_name' => $this->foodRestaurant->restaurant->name,
            'quantity' => $this->quantity,
            'discount_prices' => $this->discount_prices,
            // 'order' => [
            //     'id' => $this->order->user->name,
            //     'user_id' => $this->user_id,
            //     'created_at' => $this->order->created_at,
            //     'status_id' => $this->order->status_id,
            // ],
            // 'food' => [
            //     'id' => $this->foodRestaurant->food->id ?? null,
            //     'name' => $this->foodRestaurant->food->name ?? null,
            //     'images' => $this->foodRestaurant->food->foodViewImages->map(function ($image) {
            //         return $image->upload_url;
            //     }),
            // ],
        ];
    }

    public function with(Request $request)
    {
        return [
            'version' => '1.0.0',
            'api_url' => url('http://api.dailyfairdeal.com/api/orderDetail'),
            'message' => 'Your action is successful'
        ];
    }
}
