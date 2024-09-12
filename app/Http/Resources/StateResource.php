<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateResource extends JsonResource
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
            'country_id'=>$this->country_id
        ];

     }

     public function with(Request $request)
     {
         return[
             'version' => '1.0.0',
             'api_url' => url("http://api.dailyfairdeal.com/api/state"),
             'message' => 'Your action is successful'
         ];
     }
}
