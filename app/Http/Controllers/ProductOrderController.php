<?php

namespace App\Http\Controllers;

use Exception;
use Stripe\Charge;
use Stripe\Stripe;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use App\Mail\PaymentSuccessMail;
use App\Models\ProductOrderDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Http\Requests\TransactionRequest;
use Darryldecode\Cart\Facades\CartFacade as Cart;

class ProductOrderController extends Controller
{
    public function checkout(Request $request)
    {
        $cartItems = Cart::getContent();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

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

    public function processPayment(TransactionRequest $request)
    {

        $validatedData = $request->validated();
        // Retrieve the order using the order_id
        $order = ProductOrder::findOrFail($validatedData['order_id']);
        // dd($order);
        // Ensure the order has a shop relationship
        $shop = $order->shop; // Assuming an Order belongs to a Shop
        // dd($shop);
        if (!$shop || !$shop->email) {
            return response()->json(['error' => 'Shop email not found'], 400);
        }

        // Check if the payment has already been processed for this order
        $existingTransaction = Transaction::where('order_id', $order->id)->first();
        // dd($existingTransaction);
        if ($existingTransaction) {
            // If a transaction exists, return a response indicating the payment has already been processed
            return response()->json([
                'message' => 'Payment already processed for this order.',
                'transaction' => $existingTransaction
            ], 200);
        }


        try {
            // Set Stripe Secret Key
            Stripe::setApiKey(config('services.stripe.secret'));
            $amountInSatang = $order->final_price * 100;

            // Create Stripe Charge
            $charge = Charge::create([
                'amount' => $amountInSatang, // Convert to cents
                'currency' => 'MMK',
                'source' => $request->stripeToken,
                'description' => "Payment for Order #" . $order->id,
            ]);

            // Store Transaction
            $transaction=Transaction::create([
                'order_id' => $order->id,
                'transaction_id' => $charge->id,
                'amount' => $order->final_price,
                'currency' => 'MMK',
                'payment_method' => 'stripe',
                'status' => $charge->status,
            ]);
            // dd($transaction);

            // Optionally, update the order status to 'Paid' or similar
            $order->update(['status_id' => 2]);  // Assuming '2' means 'Paid'


            // Send email to shop owner
            Mail::to($shop->email)->send(new PaymentSuccessMail($order, $transaction));

            return response()->json([
                'message' => 'Payment successful, email sent to shop!',
                'transaction' => $transaction,
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Payment failed: ' . $e->getMessage()], 500);
        }
    }
}
