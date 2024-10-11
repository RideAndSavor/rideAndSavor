<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user_id' => $this->user_id ?? '',
            'status_id' => $this->status_id ?? '',
            'delivery_price' => $this->delivery_price ?? '',
            'total_amount' => $this->total_amount ?? '',
            'total_discount_amount' => $this->total_discount_amount ?? '',
            'comment' => $this->comment ?? '',

        ];
    }

    public function with(Request $request)
    {
        return [
            'version' => '1.0.0',
            'api_url' => url('http://api.dailyfairdeal.com/api/order'),
            'message' => 'Your action is successful'
        ];
    }
}
