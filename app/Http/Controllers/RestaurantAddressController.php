<?php

namespace App\Http\Controllers;

use App\Contracts\LocationInterface;
use App\Exceptions\CrudException;
use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use App\Traits\AddressTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class RestaurantAddressController extends Controller
{
    use AddressTrait;

    private $addressInterface;

    public function __construct(LocationInterface $locationInterface)
    {
        $this->addressInterface = $locationInterface;
        $this->getAddressInterface($locationInterface);
    }

    public function index()
    {
        try {
            $address = $this->locationInterface->all('Address');
            return AddressResource::collection($address);
        } catch (\Exception $e) {
            return CrudException::emptyData();
        }
    }

    public function store(AddressRequest $addressRequest)
    {
        $validatedData = $addressRequest->validated();
        $address = $this->storeAddress($validatedData);
        if ($address instanceof JsonResponse) {
            $address;
        }
        return new AddressResource($address);
    }


    public function update(AddressRequest $addressRequest, string $id)
    {
        $validatedData = $addressRequest->validated();
        $address = $this->updateAddress($validatedData, $id);
        if ($address instanceof JsonResponse) {
            return $address; // Return JSON response if street_id cannot be changed
        }
        return new AddressResource($address);
    }


    public function destroy(string $id)
    {
        $this->deleteAddress($id);
        return response()->json([
            'message' => Config::get('variable.ADDRESS_DELETED_SUCCESSFULLY')
        ], Config::get('variable.NO_CONTENT'));
    }
}
