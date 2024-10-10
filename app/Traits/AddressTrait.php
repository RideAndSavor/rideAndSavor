<?php

namespace App\Traits;

use App\Models\Street;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;

trait AddressTrait
{
    public $locationInterface;

    public function getAddressInterface($locationInterface)
    {
        $this->locationInterface = $locationInterface;
    }

    public function storeAddress($validatedData)
    {
        $streetData = Street::find($validatedData['street_id']);
        $fullAddress = $this->buildFullAddress($validatedData, $streetData);
        $coordinates = $this->geocodeAddress($fullAddress);

        $validatedData['latitude'] = $coordinates['latitude'];
        $validatedData['longitude'] = $coordinates['longitude'];

        $address = $this->locationInterface->store('Address', $validatedData);
        if (!$address) {
            return response()->json([
                'message' => Config::get('variable.FAILED_TO_CREATE_ADDRESS')
            ], Config::get('variable.CLIENT_ERROR'));
        }
        return $address;
    }

    protected function buildFullAddress($validatedData, $streetData)
    {
        $blockName = $validatedData['block_no'];
        $floor = $validatedData['floor'];
        $streetName = $streetData->name;
        $wardName = $streetData->ward->name;
        $townshipName = $streetData->ward->township->name;
        $cityName = $streetData->ward->township->city->name;
        $countryName = $streetData->ward->township->city->state->country->name;

        return "$blockName, $floor, $streetName, $wardName, $townshipName, $cityName, $countryName";
    }

    protected function geocodeAddress($fullAddress)
    {
        $result = app('geocoder')->geocode($fullAddress)->get();

        if (count($result) > 0) {
            $coordinates = $result[0]->getCoordinates();
            return [
                'latitude' => $coordinates->getLatitude(),
                'longitude' => $coordinates->getLongitude()
            ];
        }
        return null;
    }

    public function updateAddress($validatedData, $id)
    {
        $addressData = $this->locationInterface->findById('Address', $id);
        if (!$addressData) {
            return response()->json([
                'message' => Config::get('variable.ADDRESS_NOT_FOUND')
            ], Config::get('variable.CLIENT_ERROR'));
        }

        $addressData->street_id = $validatedData['street_id'];

        if ($addressData->isDirty('street_id')) {
            return response()->json([
                'message' => Config::get('variable.YOUR_STREET_CAN_NOT_CHANGE')
            ], Config::get('variable.SEVER_ERROR'));
        }

        return $this->locationInterface->update('Address', $validatedData, $id);
    }

    public function deleteAddress($id)
    {
        $address = $this->locationInterface->findById('Address', $id);
        if (!$address) {
            return response()->json([
                'message' => Config::get('variable.ADDRESS_NOT_FOUND')
            ], Config::get('variable.SEVER_ERROR'));
        }
        $this->locationInterface->delete('Address', $id);
        return $address;
    }

    public function updateFoodTopping($validateData, $id)
    {
        if($validateData['topping_id']){
            $topping_id = $validateData['topping_id'];
    }
    $food = $this->foodInterface->findById('Food', $id);
    if (!$food) {
        return response()->json([
            'message' => Config::get('variable.FOOD_NOT_FOUND')
        ], Config::get('variable.SEVER_NOT_FOUND'));
    }

    unset($validateData['topping_id']);

    $updatedFood = $this->foodInterface->update('Food', $validateData ,$id);
    if (!$updatedFood) {
        return response()->json([
            'message' => Config::get('variable.FOOD_UPDATE_FAILED')
        ], Config::get('variable.SEVER_ERROR'));
    }
    if (isset(request()->topping_id)) {
        $food->toppings()->sync($topping_id);
    }
    return $updatedFood;
   }
    public function dateFormat($validatedData)
    {
        $opentime = Carbon::createFromFormat('g:i A', $validatedData['open_time'])->format('g:i A');
        $closetime = Carbon::createFromFormat('g:i A', $validatedData['close_time'])->format('g:i A');
        $validatedData['open_time'] = $opentime;
        $validatedData['close_time'] = $closetime;

        return $validatedData;
    }

    public function deletedFoodTopping($id){
        $food = $this->foodInterface->findById('Food', $id);

        if (!$food) {
            return response()->json([
                'message' => Config::get('variable.FOOD_NOT_FOUND')
            ], Config::get('variable.SEVER_ERROR'));
        }
        $food->toppings()->detach();

        

        $this->foodInterface->delete('Food', $id);
        return $food;
    }
}

