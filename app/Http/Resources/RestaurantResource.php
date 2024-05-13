<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'=>$this->id,
            'address_id'=>$this->address_id,
            'name'=>$this->name,
            'open_time'=>$this->open_time,
            'close_time'=>$this->close_time,
            'phone_number'=>$this->phone_number,
        ];
    }

    public function with(Request $request)
    {
        return[
            'version' => '1.0.0',
            'api_url' => url('http://127.0.0.1:8000/api/restaurant'),
            'message' => 'Your action is successful'
        ];
    }
}
