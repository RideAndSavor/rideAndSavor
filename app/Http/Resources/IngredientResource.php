<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IngredientResource extends JsonResource
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
            'price'=>$this->price
        ];
    }

    public function with(Request $request)
    {
        return[
            'version' => '1.0.0',
            'api_url' => url('http://127.0.0.1:8000/api/ingredient'),
            'message' => 'Your action is successful'
        ];
    }
}
