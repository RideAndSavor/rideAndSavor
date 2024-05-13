<?php

namespace App\Http\Controllers;

use App\Contracts\LocationInterface;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Street;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    private $locationInterface;

    public function __construct(LocationInterface $locationInterface)
    {
        $this->locationInterface = $locationInterface;
    }

    public function index()
    {
        $addressData = $this->locationInterface->all('Address');
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
                'message' => 'Something wrong and please try again!'
            ], 401);
        }
        $address->users()->attach(auth()->user()->id, ['created_at' => now(), 'updated_at' => now()]);
        return new AddressResource($address);
    }

    public function update(Request $request, string $id)
    {
    }


    public function destroy(string $id)
    {
    }
}
