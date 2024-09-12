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
        $sub_category = SubCategory::find($this->sub_category_id);
        $foodDatas = $this->restaurants()->wherePivot('food_id', $this->id)->get();
        return [
            'name' => $this->name,
            'quantity' => $this->quantity,
            'sizes_prcies' => $foodDatas->map(function ($foodData) {
                $sizes_data = Size::find($foodData->pivot->size_id);
                return [
                    'price' => $foodData->pivot->price,
                    'size' => $sizes_data->name
                ];
            }),
            'sub_category_id ' => $sub_category->name,
            'ingredients' =>  IngredientResource::collection($this->ingredients),
            'restaurant' => new RestaurantResource($this->restaurants->first())
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
