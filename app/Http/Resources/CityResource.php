<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
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
            'name'=>$this->name,
            'state_id'=>$this->state_id
        ];
    }

    public function with(Request $request)
    {
        return[
            'version' => '1.0.0',
            'api_url' => url('http://api.dailyfairdeal.com/api/city'),
            'message' => 'Your action is successful'
        ];
    }
}
