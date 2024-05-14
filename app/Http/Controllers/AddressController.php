<?php

namespace App\Http\Controllers;

use App\Contracts\LocationInterface;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Street;
use Illuminate\Support\Facades\Config;

class AddressController extends Controller
{
    private $locationInterface;

    public function __construct(LocationInterface $locationInterface)
    {
        $this->locationInterface = $locationInterface;
    }

    public function index()
    {
        $addressData = $this->locationInterface->relationData('Address', 'users');
        return AddressResource::collection($addressData);
    }

    public function store(AddressRequest $addressRequest)
    {
        $vaildatedData = $addressRequest->validated();
        $streetData =  Street::findOrFail($vaildatedData['street_id']);
        $streetName = $streetData->name;
        $wardName = $streetData->ward->name;
        $townshipName = $streetData->ward->township->name;
        $cityName = $streetData->ward->township->city->name;
        $countryName  = $streetData->ward->township->city->state->country->name;
        $blockName = $vaildatedData['block_no'];
        $floor = $vaildatedData['floor'];
        $fullAddress = $blockName . ', ' . $floor . ', ' . $streetName . ', ' . $wardName .
            ', ' . $townshipName . ', ' . $cityName . ', ' . $countryName;
        $result = app('geocoder')->geocode($fullAddress)->get();
        $coordinates = $result[0]->getCoordinates();
        $vaildatedData['latitude'] = $coordinates->getLatitude();
        $vaildatedData['longitude'] = $coordinates->getLongitude();
        $address = $this->locationInterface->store('Address', $vaildatedData);
        if (!$address) {
            return response()->json([
                'message' => Config::get('variable.FAILED_TO_CREATE_ADDRESS')
            ], Config::get('variable.CLIENT_ERROR'));
        }
        $address->users()->attach(auth()->user()->id);
        return new AddressResource($address);
    }

    public function update(AddressRequest $addressRequest, string $id)
    {
        $validatedData = $addressRequest->validated();
        $addressData = $this->locationInterface->findById('Address', $id);
        if (!$addressData) {
            return response()->json([
                'message' => 'Address Not Found'
            ], 401);
        }
        $addressData->street_id = $validatedData['street_id'];

        if ($addressData->isDirty('street_id')) { // This will be shown if the street_id has changed
            return response()->json([
                'message' => Config::get('variable.YOUR_STREET_CAN_NOT_CHANGE')
            ], Config::get('variable.SEVER_ERROR'));
        }

        $address = $this->locationInterface->update('Address', $validatedData, $id);
        return new AddressResource($address);
    }

    public function destroy(string $id)
    {
        $address = $this->locationInterface->findById('Address', $id);
        if (!$address) {
            return response()->json([
                'message' => Config::get('variable.ADDRESS_NOT_FOUND')
            ], Config::get('variable.SEVER_ERROR'));
        }
        $this->locationInterface->delete('Address', $id);
        $address->users()->detach(auth()->user()->id);
        return response()->json([
            'message' => Config::get('variable.ADDRESS_DELETED_SUCCESSFULLY')
        ], Config::get('variable.NO_CONTENT'));
    }
}
