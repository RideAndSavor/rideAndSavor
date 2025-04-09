<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
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
            'city' => $this->street->ward->township->city->name,
            'ward' => $this->street->ward->name,
            'street' => $this->street->name,
            'block_no' => $this->block_no,
            'floor' => $this->floor,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
        ];
    }

    public function with(Request $request)
    {
        return [
            'version' => '1.0.0',
            'api_url' => url('http://api.dailyfairdeal.com/api/address'),
            'message' => "You are action is successful!"
        ];
    }
}
