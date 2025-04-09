<?php

namespace App\Http\Controllers;

use Stripe\OAuth;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Account;
use App\Models\Shop;
use App\Models\User;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use App\Mail\PaymentSuccessMail;
use Illuminate\Support\Facades\Log;
use App\Models\StripePaymentAccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class StripeController extends Controller
{

 public function redirectToStripe()
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $shop = Shop::where('user_id', $user->id)->first();
        if (!$shop) {
            return response()->json(['error' => 'Shop not found'], 404);
        }

        $jwtToken = request()->bearerToken();

        $url = "https://connect.stripe.com/oauth/authorize?" . http_build_query([
            'response_type' => 'code',
            'client_id' => config('services.stripe.client_id'),
            'scope' => 'read_write',
            'redirect_uri' => config('services.stripe.redirect_uri'),
            'state' => urlencode($jwtToken),
            'stripe_user[email]' => $user->email, // Pre-fill email to encourage login
            'always_prompt' => 'true', // Forces login instead of creating a new account
        ]);

        return response()->json(['url' => $url]);
    }


public function handleStripeCallback(Request $request)
    {

        // Check if it's a GET request, as Stripe sends a GET request
        if ($request->isMethod('get')) {
            // Handle the logic here as if it were a POST request
            $stripeCode = $request->code;
            $jwtToken = $request->state;

            // Authenticate the user using the JWT token passed in the state parameter
            if ($jwtToken) {
                JWTAuth::setToken($jwtToken);
                $user = JWTAuth::toUser();
            } else {
                return response()->json(['error' => 'User not authenticated'], 401);
            }

            // Proceed with the rest of your logic
            Stripe::setApiKey(config('services.stripe.secret'));

            try {
                $response = OAuth::token([
                    'grant_type' => 'authorization_code',
                    'code' => $stripeCode,
                ]);

                $stripeAccountId = $response->stripe_user_id;

                $shop = Shop::where('user_id', $user->id)->first(['id']);

                if (!$shop) {
                    return response()->json(['error' => 'Shop not found'], 404);
                }

                // Store the Stripe account ID
                StripePaymentAccount::updateOrCreate(
                    ['shop_id' => $shop->id],
                    ['stripe_account_id' => $stripeAccountId]
                );

                return response()->json(['message' => 'Stripe account connected successfully!']);
            } catch (\Exception $e) {
                return response()->json(['error' => 'Failed to connect Stripe: ' . $e->getMessage()], 500);
            }
        }

        // If it's not a GET request, return a method not allowed response
        return response()->json(['error' => 'Invalid request method. Only GET is allowed'], 405);
    }

    public function processStripePayment($order, $shop, $stripeToken)
    {
        // Retrieve the shop's Stripe account ID
        $shopStripeAccount = StripePaymentAccount::where('shop_id', $shop->id)->first();

        if (!$shopStripeAccount || !$shopStripeAccount->stripe_account_id) {
            return response()->json(['error' => 'Shop does not have a connected Stripe account'], 400);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        // Calculate minimum required MMK amount
        $usdToMmkRate = 0.00024;
        $minAmountMMK = ceil(50 / $usdToMmkRate);
        $chargeAmount = max($order->final_price * 100, $minAmountMMK);

        try {
            // Attempt to charge the customer
            $charge = Charge::create([
                'amount' => $chargeAmount,
                'currency' => 'mmk',
                'source' => $stripeToken,
                'description' => "Payment for Order #" . $order->id,
                'transfer_data' => [
                    'destination' => $shopStripeAccount->stripe_account_id,
                ],
            ]);

            if ($charge->status !== 'succeeded') {
                Log::error("Payment failed for Order #{$order->id}: Charge status - {$charge->status}");

                return response()->json([
                    'error' => 'Payment was not successful. Please try again or use a different card.',
                ], 402);
            }

            // Store transaction
            $transaction = Transaction::create([
                'order_id' => $order->id,
                'transaction_id' => $charge->id,
                'amount' => $order->final_price,
                'currency' => 'MMK',
                'payment_method' => 'stripe',
                'status' => 'succeeded',
            ]);

            // Update order status to "Paid"
            $order->update(['status_id' => 2]);

            // 🔹 Reduce stock quantity for each product in the order
            try {
                $order = ProductOrder::with('orderDetails')->findOrFail($order->id);
                Log::info("✅ Order Found: Order ID {$order->id}");

                if ($order->orderDetails->isEmpty()) {
                    Log::error("❌ Order ID {$order->id} has NO order details!");
                    return response()->json(['error' => 'No order details found.'], 400);
                }

            } catch (\Exception $e) {
                Log::error("❌ Error fetching order details: " . $e->getMessage());
                return response()->json(['error' => 'Failed to fetch order details. Please check logs.'], 500);
            }

            // Send email to shop owner
            Mail::to($shop->email)->send(new PaymentSuccessMail($order, $transaction));

            return response()->json([
                'message' => 'Payment successful via Stripe, email sent to shop!',
                'transaction' => $transaction,
            ]);

        } catch (\Stripe\Exception\CardException $e) {
            Log::error("Stripe Card Error: " . $e->getMessage());
            return response()->json(['error' => 'Your card was declined: ' . $e->getMessage()], 402);
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            Log::error("Stripe Invalid Request: " . $e->getMessage());
            return response()->json(['error' => 'Invalid payment request. Please contact support.'], 400);
        } catch (\Stripe\Exception\ApiErrorException $e) {
            Log::error("Stripe API Error: " . $e->getMessage());
            return response()->json(['error' => 'Payment failed due to a Stripe error. Please try again later.'], 500);
        } catch (\Exception $e) {
            Log::error("Payment Processing Error: " . $e->getMessage());
            return response()->json(['error' => 'An unexpected error occurred. Please try again.'], 500);
        }
    }







    public function getShopStripeAccount($shop_id)
{
    // Find the shop by ID
    $shop = Shop::find($shop_id);

    // Check if the shop has a connected Stripe account
    if ($shop && $shop->paymentAccount) {
        return response()->json([
            'stripe_account_id' => $shop->paymentAccount->stripe_account_id
        ]);
    } else {
        return response()->json(['error' => 'Shop is not connected to Stripe'], 404);
    }
}

}

