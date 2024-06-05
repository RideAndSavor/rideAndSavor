<?php

namespace App\Http\Controllers;

use ArgumentCountError;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Exceptions\CrudException;
use App\Http\Requests\PriceRequest;
use App\Contracts\LocationInterface;
use App\Http\Resources\PriceResource;
use Illuminate\Support\Facades\Config;

class PriceController extends Controller
{
    private $priceInterface;

    public function __construct(LocationInterface $priceInterface)
    {
        $this->priceInterface = $priceInterface;
    }

    public function index()
    {
        try {
            $price = $this->priceInterface->all('Price');

            return PriceResource::collection($price);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseWithConfigError($e);
        }
    }

    public function store(PriceRequest $request)
    {
        $validatedData = $request->validated();
        try {
            $price = $this->priceInterface->store('Price', $validatedData);
            return new PriceResource($price);
        } catch (ArgumentCountError $e) {
            throw CrudException::argumentCountError();
        }
    }

    public function update(PriceRequest $request, string $id)
    {
        $validateData = $request->validated();
        try {
            $this->priceInterface->findById('Price', $id);
            $updatePrice = $this->priceInterface->update('Price', $validateData, $id);
            return new PriceResource($updatePrice);
        } catch (\Exception $e) {
            throw CrudException::prepareDataFormat();
        }
    }

    public function destroy(String $id)
    {
        $price = $this->priceInterface->findById('Price' , $id);
        if(!$price)
        {
            return response()->json([
                'message' => Config::get('variable.PNF')
            ], Config::get('variable.SEVER_ERROR'));
        }
        $this->priceInterface->delete('Price', $id);
        return response()->json([
            'message' => Config::get('variable.PDS')
        ], Config::get('variable.OK'));
    }
}
