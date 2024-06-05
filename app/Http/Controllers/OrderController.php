<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Exceptions\CrudException;
use App\Http\Requests\OrderRequest;
use App\Contracts\LocationInterface;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Config;

class OrderController extends Controller
{
    private $orderInterface;

    public function __construct(LocationInterface $orderInterface )
    {
     $this->orderInterface = $orderInterface;
    }
    public function index()
    {
        try {
            $order = $this->orderInterface->all('Order');
            return OrderResource::collection($order);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseWithConfigError($e);
        }
     }

    public function store(OrderRequest $request)
    {
        $validateData = $request->validated();
        try {
            $order = $this->orderInterface->store('Order',$validateData);
        return new OrderResource($order);
        } catch (\Exception $e) {
            throw CrudException::argumentCountError();
        }
    }

    public function update(OrderRequest $request, string $id)
    {
    try {
        $validateData = $request->validated();
        $order = $this->orderInterface->findById('Order',$id);
        if(!$order){
            return response()->json([
                'message'=>Config::get('variable.ONF')
            ],Config::get('variable.CLIENT_ERROR'));
        }
        $order = $this->orderInterface->update('Order',$validateData,$id);
        return new OrderResource($order);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseWithConfigError($e);
        }
    }

    public function destroy(string $id)
    {
        $order = $this->orderInterface->findById('Order',$id);
        if(!$order){
            return response()->json([
                'message'=>Config::get('variable.ONF')
            ],Config::get('variable.SEVER_ERROR'));
        }
        $order = $this->orderInterface->delete('Order',$id);
        return response()->json([
            'message'=>Config::get('variable.ODS')
        ],Config::get('variable.OK'));
    }

    public function getRecentOrder($userId) {
        $orders = Order::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->with(['orderDetalis.foodRestaurant.food.image'])
            ->get();

            // dd($orders);

        $recentOrders = $orders->map(function ($order) {
            return $order->orderDetalis->map(function ($orderDetail) {
                if ($orderDetail->foodRestaurant && $orderDetail->foodRestaurant->food) {
                    $food = $orderDetail->foodRestaurant->food;
                    return [
                        'order_id' => $orderDetail->id,
                        'food_name' => $food->name,
                        'food_image' => $food->image->upload_url,  // assuming 'upload_url' is the column name in images table
                    ];
                }
                return null;
            })->filter(); // Filter out null values
        })->flatten();

        return response()->json($recentOrders);
    }

}
