<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use App\Models\Address;
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
        // $open_time = Carbon::parse($this->open_time)->format('g:i A');
        // $close_time = Carbon::parse($this->close_time)->format('g:i A');
        $address = Address::findOrFail($this->address_id);
        return [
            'id' => $this->id,
            // 'address_id' => $this->address_id,
            'name' => $this->name,
            'restaurant_type' => $this->restaurantType->name,
            'avg_rating' => $this->ratings_avg_rating_id,
            'open_time' => $this->open_time,
            'close_time' => $this->close_time,
            'phone_number' => $this->phone_number,
            'user_name' => $this->user->name,
            'orderDetail' => OrderDetailResource::collection($this->whenLoaded('foodRestaurants', fn() => $this->foodRestaurants->flatMap->orderDetails)),
            'images' => ImageResource::collection($this->whenLoaded('restaurantImages')),
            'comments' => CommentResource::collection($this->whenLoaded('comments')),
            //to write with address resources
            //'address' => new AddressResource($this->whenLoaded('address')),
            'floor' =>$address->floor,
            'block_no'=>$address->block_no,
            'Street_Name' => $address->street->name,
            'Ward_Name' => $address->street->ward->name,
            'TownShip_Name' => $address->street->ward->township->name,
            'City_Name' => $address->street->ward->township->city->name,
            'Country_Name' => $address->street->ward->township->city->state->country->name,
            'Latitude' => $address->latitude,
            'Longitude' => $address->longitude,
        ];
    }

    public function with(Request $request)
    {
        return [
            'version' => '1.0.0',
            'api_url' => url('http://api.dailyfairdeal.com/api/restaurant'),
            'message' => 'Your action is successful'
        ];
    }
}
