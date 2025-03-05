<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BiddingPriceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // dd($request);
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'travel_id' => $this->travel_id,
            'taxi_driver_id' => $this->taxi_driver_id,
            'price' => $this->price,
        ];
    }
    public function with(Request $request)
    {
        return[
            'version' => '1.0.0',
            'api_url' => url('http://api.dailyfairdeal.com/api/status'),
            'message' => 'Your action is successful'
        ];
    }
}
