<?php

namespace App\Http\Controllers;

use Stripe\OAuth;
use Stripe\Charge;
use Stripe\Stripe;
use Stripe\Account;
use App\Models\Shop;
use App\Models\User;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Mail\PaymentSuccessMail;
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

    // Calculate minimum required MMK amount (if applicable)
    $usdToMmkRate = 0.00024;
    $minAmountMMK = ceil(50 / $usdToMmkRate);
    $chargeAmount = max($order->final_price * 100, $minAmountMMK);

    try {
        // Create a charge that goes directly to the shop's Stripe account
        $charge = Charge::create([
            'amount' => $chargeAmount,
            'currency' => 'mmk',
            'source' => $stripeToken,
            'description' => "Payment for Order #" . $order->id,
            'transfer_data' => [
                'destination' => $shopStripeAccount->stripe_account_id, // Direct payment to shop's Stripe account
            ],
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
    } catch (\Exception $e) {
        return response()->json(['error' => 'Payment failed: ' . $e->getMessage()], 500);
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

