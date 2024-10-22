<?php

namespace App\Http\Resources;

use App\Models\Size;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FoodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $foodDatas = $this->restaurants()->wherePivot('food_id', $this->id)->get();
        return [
            'food_name' => $this->name,
            'food_image' => ImageResource::collection($this->whenLoaded('foodImages')),
            'category' => new CategoryResource($this->whenLoaded('subCategory', fn() => $this->subCategory?->category)),
            'sub_category' => new SubCategoryResource($this->whenLoaded('subCategory')),
            'sizes_prcies' => $foodDatas->map(function ($foodData) {
                $sizes_data = Size::find($foodData->pivot->size_id);
                return [
                    'price' => $foodData->pivot->price,
                    'size' => $sizes_data->name
                ];
            }),
            'toppings' => ToppingResource::collection($this->whenLoaded('toppings')),
            // 'restaurant' => new RestaurantResource($this->restaurants->first())
        ];
    }

    public function with(Request $request)
    {
        return [
            'version' => '1.0.0',
            'api_url' => url('http://api.dailyfairdeal.com/api/food'),
            'message' => 'Your action is successful'
        ];
    }
}
