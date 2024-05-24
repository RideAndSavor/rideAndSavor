<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Exceptions\CrudException;
use App\Contracts\LocationInterface;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\DeliveryPriceRequest;
use App\Http\Resources\DeliveryPriceResource;

class DeliveryPriceController extends Controller
{
    private $deliveryPriceInterface;
 
    public function __construct(LocationInterface $deliveryPriceInterface ) {
     $this->deliveryPriceInterface = $deliveryPriceInterface;
    }
     public function index()
     {
         try {
             $delivery_price = $this->deliveryPriceInterface->all('DeliveryPrice');
             return DeliveryPriceResource::collection($delivery_price);
         } catch (\Exception $e) {
            return ResponseHelper::jsonResponseWithConfigError($e);
         }
     }
 
     public function store(DeliveryPriceRequest $request)
     {
         $validateData = $request->validated();
         try {
             $delivery_price = $this->deliveryPriceInterface->store('DeliveryPrice',$validateData);
         return new DeliveryPriceResource($delivery_price);
         } catch (\Exception $e) {
             throw CrudException::argumentCountError();
         }
     }
 
     public function update(DeliveryPriceRequest $request, string $id)
     {
         $validateData = $request->validated();
         try {
             $this->deliveryPriceInterface->findById('DeliveryPrice', $id);
             $updateDeliveryPrice = $this->deliveryPriceInterface->update('DeliveryPrice', $validateData, $id);
             return new DeliveryPriceResource($updateDeliveryPrice);
         } catch (\Exception $e) {
             throw CrudException::prepareDataFormat();
         }
     }
 
     public function destroy(string $id)
     {
         $delivery_price = $this->deliveryPriceInterface->findById('DeliveryPrice',$id);
         if(!$delivery_price){
             return response()->json([
                 'message'=>Config::get('variable.DPNF')
             ],Config::get('variable.SEVER_ERROR'));
         }
         $delivery_price = $this->deliveryPriceInterface->delete('DeliveryPrice',$id);
         return response()->json([
             'message'=>Config::get('variable.DPDS')
         ],Config::get('variable.OK'));
     }
     
 }
