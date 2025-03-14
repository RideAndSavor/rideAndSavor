<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ElectronicResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'description' => $this->description,
            'price' => $this->price,
            'discount' => $this->discount,
            'stock_quantity' => $this->stock_quantity,
            'warranty' => $this->warranty,
            'status' => $this->status,
            'images' => ImageResource::collection($this->whenLoaded('images')),
        ];
    }

    /**
     * Additional meta information for the response.
     */
    public function with(Request $request)
    {
        return [
            'version' => '1.0.0',
            'api_url' => url('/api/electronics'),
            'message' => 'Your action is successful',
        ];
    }
}
