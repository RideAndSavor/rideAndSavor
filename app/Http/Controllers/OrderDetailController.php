<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Exceptions\CrudException;
use App\Contracts\LocationInterface;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\OrderDetailRequest;
use App\Http\Resources\OrderDetailResource;

class OrderDetailController extends Controller
{
    private $orderDetailInterface;

    public function __construct(LocationInterface $orderDetailInterface )
    {
     $this->orderDetailInterface = $orderDetailInterface;
    }
    public function index()
    {
        try {
            $orderDetail = $this->orderDetailInterface->all('OrderDetail');
            return OrderDetailResource::collection($orderDetail);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseWithConfigError($e);
        }
     }

    public function store(OrderDetailRequest $request)
    {
        $validateData = $request->validated();
        try {
            $orderDetail = $this->orderDetailInterface->store('OrderDetail',$validateData);
        return new OrderDetailResource($orderDetail);
        } catch (\Exception $e) {
            throw CrudException::argumentCountError();
        }
    }

    public function update(OrderDetailRequest $request, string $id)
    {
    try {
        $validateData = $request->validated();
        $orderDetail = $this->orderDetailInterface->findById('OrderDetail',$id);
        if(!$orderDetail){
            return response()->json([
                'message'=>Config::get('variable.ODNF')
            ],Config::get('variable.CLIENT_ERROR'));
        }
        $orderDetail = $this->orderDetailInterface->update('OrderDetail',$validateData,$id);
        return new OrderDetailResource($orderDetail);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseWithConfigError($e);
        }
    }

    public function destroy(string $id)
    {
        $orderDetail = $this->orderDetailInterface->findById('OrderDetail',$id);
        if(!$orderDetail){
            return response()->json([
                'message'=>Config::get('variable.ODNF')
            ],Config::get('variable.SEVER_ERROR'));
        }
        $orderDetail = $this->orderDetailInterface->delete('OrderDetail',$id);
        return response()->json([
            'message'=>Config::get('variable.ODDS')
        ],Config::get('variable.OK'));
    }
}
