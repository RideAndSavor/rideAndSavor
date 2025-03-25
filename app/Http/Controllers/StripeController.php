<?php

namespace App\Http\Controllers;

use Stripe\OAuth;
use Stripe\Stripe;
use Stripe\Account;
use App\Models\Shop;
use Illuminate\Http\Request;
use App\Models\StripePaymentAccount;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    /**
     * Redirect shop owner to Stripe for account creation
     */
    public function connectShopToStripe(Request $request)
    {
        $connectUrl = "https://connect.stripe.com/oauth/authorize?response_type=code"
            . "&client_id=" . config('services.stripe.client_id')
            . "&scope=read_write"
            . "&redirect_uri=" . route('stripe.callback');

        return response()->json(['url' => $connectUrl]);
    }


    /**
     * Retrieve Shop Stripe Account Info
     */
    public function handleStripeCallback(Request $request)
    {
        try {
            // Get the code from the Stripe callback
            $code = $request->code;

            // Exchange the code for an access token and Stripe account ID
            $response = OAuth::token([
                'grant_type' => 'authorization_code',
                'code' => $code,
            ]);

            // Retrieve the shop owner's Stripe account ID
            $stripeAccountId = $response->stripe_user_id;

            // Save the Stripe account ID to the database for the shop
            $shop = auth()->user()->shop; // Get the authenticated user's shop
            StripePaymentAccount::updateOrCreate(
                ['shop_id' => $shop->id],
                ['stripe_account_id' => $stripeAccountId]
            );

            // Return the Stripe account ID as a response
            return response()->json([
                'message' => 'Shop successfully connected to Stripe!',
                'stripe_account_id' => $stripeAccountId
            ]);
        } catch (\Exception $e) {
            // Handle any errors that occur during the process
            return response()->json(['error' => 'Stripe connection failed: ' . $e->getMessage()], 500);
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

