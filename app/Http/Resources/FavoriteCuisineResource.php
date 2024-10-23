<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteCuisineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'food-name' => $this->name,
            'food_count' => $this->food_count,
            'images' => ImageResource::collection($this->whenLoaded('foodImages'))
        ];
    }
}
