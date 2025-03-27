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
use App\Http\Controllers\StripeController;
use Darryldecode\Cart\Facades\CartFacade as Cart;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class ProductOrderController extends Controller
{
    protected $stripeController;

    public function __construct(StripeController $stripeController)
    {
        $this->stripeController = $stripeController;
    }

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

    public function processPayment(Request $request)
{
    $request->validate([
        'order_id' => 'required|exists:product_orders,id',
        'payment_method' => 'required|in:paypal,stripe',
        'stripeToken' => 'required_if:payment_method,stripe', // Required for Stripe
    ]);

    // Retrieve the order
    $order = ProductOrder::findOrFail($request->order_id);
    $shop = $order->shop;

    if (!$shop || !$shop->email) {
        return response()->json(['error' => 'Shop email not found'], 400);
    }

    // Check if payment is already processed
    $existingTransaction = Transaction::where('order_id', $order->id)->first();
    if ($existingTransaction) {
        return response()->json([
            'message' => 'Payment already processed for this order.',
            'transaction' => $existingTransaction
        ], 200);
    }

    try {
        if ($request->payment_method === 'stripe') {
            // Process Stripe Payment
            return $this->stripeController->processStripePayment($order, $shop, $request->stripeToken);
        } elseif ($request->payment_method === 'paypal') {
            // Process PayPal Payment
            return $this->processPaypalPayment($order, $shop);
        }
    } catch (\Exception $e) {
        return response()->json(['error' => 'Payment failed: ' . $e->getMessage()], 500);
    }
}
private function processStripePayment($order, $shop, $stripeToken)
{
    Stripe::setApiKey(config('services.stripe.secret'));

    // Calculate minimum required MMK amount (if applicable)
    $usdToMmkRate = 0.00024;
    $minAmountMMK = ceil(50 / $usdToMmkRate);
    $chargeAmount = max($order->final_price * 100, $minAmountMMK);

    // Create Stripe Charge
    $charge = Charge::create([
        'amount' => $chargeAmount,
        'currency' => 'mmk',
        'source' => $stripeToken,
        'description' => "Payment for Order #" . $order->id,
    ]);

    // Store Transaction
    $transaction = Transaction::create([
        'order_id' => $order->id,
        'transaction_id' => $charge->id,
        'amount' => $order->final_price,
        'currency' => 'MMK',
        'payment_method' => 'stripe',
        'status' => $charge->status,
    ]);

    // Update order status to "Paid"
    $order->update(['status_id' => 2]);

    // Send email to shop owner
    Mail::to($shop->email)->send(new PaymentSuccessMail($order, $transaction));

    return response()->json([
        'message' => 'Payment successful via Stripe, email sent to shop!',
        'transaction' => $transaction,
    ]);
}
private function processPaypalPayment($order, $shop)
{
    try {
        // Initialize PayPal client
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('services.paypal'));
        $paypalToken = $provider->getAccessToken(); // Get PayPal token
        // dd($paypalToken);

        // Prepare PayPal order creation request
        $response = $provider->createOrder([
        "intent" => "CAPTURE",
        "purchase_units" => [
        [
            "amount" => [
            "currency_code" => "USD",
            "value" => number_format($order->final_price, 2, '.', ''),
                ]
            ]
        ],
            "application_context" => [
            "return_url" => "https://yourdomain.com/paypal/success", // PayPal success URL
            "cancel_url" => "https://yourdomain.com/paypal/cancel", // PayPal cancel URL
            "notify_url" => config('services.paypal.notify_url') // PayPal notification URL
            ]
        ]);

        // Check if the PayPal order was created successfully
        if (isset($response['id']) && $response['status'] == "CREATED") {
            foreach ($response['links'] as $link) {
                if ($link['rel'] === 'approve') {
                // Send the PayPal approval URL to the client for redirection
                return response()->json([
                'message' => 'Redirect to PayPal',
                'approval_url' => $link['href']
                ]);
                }
            }
        }

        return response()->json(['error' => 'Payment failed. Unable to create PayPal order.'], 500);
        } catch (Exception $e) {
        return response()->json(['error' => 'Payment failed: ' . $e->getMessage()], 500);
        }
}

public function successPayment(Request $request)
{
    $request->validate([
        'order_id' => 'required|exists:product_orders,id',
        'paypal_order_id' => 'required',
    ]);

    $order = ProductOrder::findOrFail($request->order_id);

    try {
        // Initialize PayPal client
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('services.paypal'));
        $paypalToken = $provider->getAccessToken();

        // Capture the payment
        $response = $provider->capturePaymentOrder($request->paypal_order_id);

        if (isset($response['status']) && $response['status'] == "COMPLETED") {
            // Save transaction details
            $transaction = Transaction::create([
                'order_id' => $order->id,
                'transaction_id' => $response['id'],
                'amount' => $order->final_price,
                'currency' => 'USD',
                'payment_method' => 'paypal',
                'status' => 'Completed',
            ]);

            // Update order status
            $order->update(['status_id' => 2]); // Mark as paid

            // Send confirmation email
            Mail::to($order->shop->email)->send(new PaymentSuccessMail($order, $transaction));

            return response()->json([
                'message' => 'Payment successful, email sent to shop!',
                'transaction' => $transaction,
            ]);
        }

        return response()->json(['error' => 'Payment not completed.'], 500);
    } catch (Exception $e) {
        return response()->json(['error' => 'Payment capture failed: ' . $e->getMessage()], 500);
    }
}


}
