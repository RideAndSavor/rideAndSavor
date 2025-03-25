<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxiDriverResource extends JsonResource
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
            'rider_id' => $this->user->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_available' => $this->is_available,
            'car_year' => $this->car_year,
            'car_make' => $this->car_make,
            'car_model' => $this->car_model,
            'car_colour' => $this->car_colour,
            'license_plate' => $this->license_plate,
            'other_info' => $this->other_info,
            'user' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
                'age'=> $this->user->age,
            ],
        ];
    }

    public function with(Request $request)
    {
        return[
            'version' => '1.0.0',
            'api_url' => url('http://api.dailyfairdeal.com/api/taxi-drivers'),
            'message' => 'Your action is successful'
        ];
    }
}
