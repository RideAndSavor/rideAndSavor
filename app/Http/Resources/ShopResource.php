<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'discount_id' => $this->discount_id,
            'slug' => $this->slug,
            'description' => $this->description,
            'website_url' => $this->website_url,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'social_media_links' => $this->social_media_links,
            'open_time' => $this->open_time,
            'close_time' => $this->close_time,
            'status' => $this->status,
            // 'images' => $this->images->pluck('upload_url'),
            'images' => ImageResource::collection($this->whenLoaded('images')),
            'address' => new AddressResource($this->whenLoaded('address')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }

    /**
     * Additional data to be included with the response.
     *
     * @return array<string, mixed>
     */
    public function with(Request $request)
    {
        return [
            'version' => '1.0.0',
            'api_url' => url('http://api.dailyfairdeal.com/api/shops'),
            'message' => 'Your action is successful',
        ];
    }
}
