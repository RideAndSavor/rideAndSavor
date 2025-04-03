<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AcceptDriverResource extends JsonResource
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
            'user_id' => $this->user_id,
            'travel_id' => $this->travel_id,
            'taxi_driver_id' => $this->taxi_driver_id,
            'price' => $this->price,
            'status' => $this->status,
        ];
    }

    /**
     * Additional data to send with the response.
     */
    public function with(Request $request): array
    {
        return [
            'version' => '1.0.0',
            'api_url' => url('http://api.dailyfairdeal.com/api/status'),
            'message' => 'Your action is successful',
        ];
    }
}
