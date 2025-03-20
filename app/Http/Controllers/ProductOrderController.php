<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use App\Models\ProductOrder;
use App\Models\ProductOrderDetail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductOrderController extends Controller
{
    public function checkout(Request $request)
    {
        $cartItems = Cart::getContent();
        // dd($cartItems);

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        // Calculate totals
        $totalPrice = 0;
        $totalDiscount = 0;
        $finalPrice = 0;

        foreach ($cartItems as $item) {
            $totalPrice += $item->attributes['total_price'];
            $totalDiscount += $item->attributes['discount_amount'];
        }

        $finalPrice = $totalPrice - $totalDiscount;

        DB::beginTransaction();
        try {
            // Create new order
            $order = ProductOrder::create([
                'user_id' => Auth::id(),
                'shop_id' => $cartItems->first()->attributes['shop_id'] ?? null, // Assuming all items belong to one shop
                'status_id' => $request->status_id, // Assuming status_id 1 is 'Pending'
                'delivery_id' => $request->delivery_id ?? null, // Handle delivery assignment
                'total_price' => $totalPrice,
                'discount_price' => $totalDiscount,
                'final_price' => $finalPrice,
                'comment' => $request->comment ?? null
            ]);

            // Insert order details
            foreach ($cartItems as $item) {
                ProductOrderDetail::create([
                    'product_order_id' => $order->id,
                    'product_id' => $item->id,
                    'quantity' => $item->quantity,
                    'unique_price' => $item->price,
                    'discount_price' => $item->attributes['discount_amount'],
                    'final_price' => $item->attributes['after_discount_price'],
                ]);
            }

            // Clear cart after order is placed
            Cart::clear();

            DB::commit();
            return response()->json(['message' => 'Order placed successfully', 'order_id' => $order->id]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Order failed', 'error' => $e->getMessage()], 500);
        }
    }
}
