<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'user_name' => new UserResource($this->whenLoaded('user')),
            'total_price' => $this->total_price,
            'cart_item_count' => $this->whenLoaded('cartItems') ? $this->cartItems->count() : $this->cartItems->count(),
            'cart_item' => CartItemsResource::collection($this->whenLoaded('cartItems')),
        ];
    }
}
