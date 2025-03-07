<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Helpers\ResponseHelper;
use App\Exceptions\CrudException;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\OrderRequest;
use App\Contracts\LocationInterface;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\OrderResource;
use Illuminate\Support\Facades\Config;

class OrderController extends Controller
{
    private $orderInterface;

    public function __construct(LocationInterface $orderInterface)
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
        $validateData['order']['user_id'] = Auth::id();
        $validateData['order']['status_id'] = 1;
        try {
            DB::beginTransaction();
            $order = $this->orderInterface->store('Order', $validateData['order']);

            foreach ($validateData['order_item'] as $orderItem) {
                $orderItem['order_id'] = $order->id;
                $this->orderInterface->store('OrderDetail', $orderItem);
            }
            DB::commit();
            return new OrderResource($order);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
    }

    public function update(OrderRequest $request, Order $order)
    {
        $validatedData = $request->validated();
        $validatedData['order']['user_id'] = Auth::id();
        $validatedData['order']['status_id'] = 1;
        try {
            DB::beginTransaction();
            $order = $this->orderInterface->update('Order', $validatedData['order'], $order->id);
            $orderItemIDs = OrderDetail::query()->where('order_id', '=', $order->id)->pluck('id')->toArray();
            foreach ($validatedData['order_item'] as $index => $orderItem) {
                $orderItem['order_id'] = $order->id;
                $this->orderInterface->update('OrderDetail', $orderItem, $orderItemIDs[$index]);
            }
            DB::commit();
            return new OrderResource($order);
        } catch (\Exception $exception) {
            DB::rollBack();
            throw new \Exception($exception->getMessage(), $exception->getCode());
        }
    }

    public function destroy(string $id)
    {
        $order = $this->orderInterface->findById('Order', $id);
        if (!$order) {
            return response()->json([
                'message' => Config::get('variable.ONF')
            ], Config::get('variable.SEVER_ERROR'));
        }
        $order = $this->orderInterface->delete('Order', $id);
        return response()->json([
            'message' => Config::get('variable.ODS')
        ], Config::get('variable.OK'));
    }

    public function getRecentOrder($userId)
    {
        $recentOrders = Order::where('user_id', $userId)
            ->with(['orderDetalis.foodRestaurant.food.image'])
            ->orderBy('created_at', 'desc')
            ->take(5) // Adjust the number of recent orders as needed
            ->get();

        // return response()->json($recentOrders);

        $foodCounts = [];

        // Loop through each order and count each food item
        foreach ($recentOrders as $order) {
            foreach ($order->orderDetalis as $orderDetail) {
                $foodId = $orderDetail->foodRestaurant->food->id;
                if (!isset($foodCounts[$foodId])) {
                    $foodCounts[$foodId] = [
                        'food' => $orderDetail->foodRestaurant->food,
                        'count' => 0,
                    ];
                }
                $foodCounts[$foodId]['count']++;
            }
        }

        // Sort the food items by count in descending order
        usort($foodCounts, function ($a, $b) {
            return $b['count'] <=> $a['count'];
        });

        // Get the most ordered item(s)
        $mostOrdered = array_slice($foodCounts, 0, 10); // Adjust the number of items to return as needed

        // Return the most ordered item(s) as a JSON response
        return response()->json($mostOrdered);
    }
}
