<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'shop' => new ShopResource($this->whenLoaded('shop')),
            'subcategory' => new SubcategoryResource($this->whenLoaded('subcategory')),
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'original_price' => $this->original_price,
            'discount_percent' => $this->discount_percent,
            'final_price' => $this->final_price,
            'stock_quantity' => $this->stock_quantity,
            'weight' => $this->weight,
            'color' => $this->color,
            'description' => $this->description,
            'status' => $this->status,
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
        ];
    }
}
