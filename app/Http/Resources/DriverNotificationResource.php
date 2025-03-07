<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverNotificationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // dd($request);
        return [
            'travel_id' => $this->travel->id,
            'pickup_location' => [
                'latitude' => $this->travel->pickup_latitude,
                'longitude' => $this->travel->pickup_longitude,
            ],
            'destination_location' => [
                'latitude' => $this->travel->destination_latitude,
                'longitude' => $this->travel->destination_longitude,
            ],
            'status' => $this->travel->status,
            'user' => [
                'id' => $this->travel->user->id,
                'name' => $this->travel->user->name,
                'email' => $this->travel->user->email,
                'phone_no' => $this->travel->user->phone_no,
                'gender' => $this->travel->user->gender,
                'age' => $this->travel->user->age,
            ],
        ];
    }
}
