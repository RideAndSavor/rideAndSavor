<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DriverDetailResources extends JsonResource
{
    public $tripDatas, $taxiData, $taxiDriverData;
    public function __construct($tripsData, $taxiData, $taxiDriverData)
    {
        $this->tripDatas = $tripsData;
        $this->taxiData = $taxiData;
        $this->taxiDriverData = $taxiDriverData;
    }

    public function toArray(Request $request): array
    {
        $tripData = json_decode($this->tripDatas[0], true); // Decode the first item in the array
        return [
            'current_location' => $tripData['current_location'],
            'destination' => $tripData['destination'],
            'price' => $tripData['price'],
            'car_year' => $this->taxiData->car_year,
            'car_make' => $this->taxiData->car_make,
            'car_colour' => $this->taxiData->car_colour,
            'license_plate' => $this->taxiData->license_plate,
            'other_info' => $this->taxiData->other_info,
            'name' => $this->taxiDriverData->name,
            'email' => $this->taxiDriverData->email,
            'phone_no' => $this->taxiDriverData->phone_no,
            'gender' => $this->taxiDriverData->gender,
            'age' => $this->taxiDriverData->age,
        ];
    }
}
