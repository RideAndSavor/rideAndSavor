<?php

namespace App\Http\Controllers;

use Stripe\OAuth;
use Stripe\Stripe;
use Stripe\Account;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\StripePaymentAccount;
use Illuminate\Support\Facades\Auth;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class StripeController extends Controller
{
    /**
     * Redirect shop owner to Stripe for account creation
     */
    // public function redirectToStripe()
    // {
    //     $shop = Shop::where('user_id', Auth::id())->first(); // Assuming shop owner is authenticated

    //     if (!$shop) {
    //         return response()->json(['error' => 'Shop not found'], 404);
    //     }

    //     $url = "https://connect.stripe.com/oauth/authorize?" . http_build_query([
    //         'response_type' => 'code',
    //         'client_id' => config('services.stripe.client_id'),
    //         'scope' => 'read_write',
    //         'redirect_uri' => config('services.stripe.redirect_uri'),
    //     ]);

    //     return response()->json(['url' => $url]);
    // }


    // /**
    //  * Retrieve Shop Stripe Account Info
    //  */
    // public function handleStripeCallback(Request $request)
    // {

    //     if ($request->has('error')) {
    //         return response()->json(['error' => $request->get('error_description')], 400);
    //     }

    //     Stripe::setApiKey(config('services.stripe.secret'));

    //     try {
    //         $response = OAuth::token([
    //             'grant_type' => 'authorization_code',
    //             'code' => $request->code,
    //         ]);

    //         $stripeAccountId = $response->stripe_user_id;

    //         $shop = Shop::where('user_id', $userId)->first(['id']);
    //         dd($shop);
    //         if (!$shop) {
    //             return response()->json(['error' => 'Shop not found'], 404);
    //         }

    //         // Store the Stripe account ID
    //         StripePaymentAccount::updateOrCreate(
    //             ['shop_id' => $shop->id],
    //             ['stripe_account_id' => $stripeAccountId]
    //         );

    //         return response()->json(['message' => 'Stripe account connected successfully!']);
    //     } catch (\Exception $e) {
    //         return response()->json(['error' => 'Failed to connect Stripe: ' . $e->getMessage()], 500);
    //     }
    // }

    public function redirectToStripe()
{
    $user = Auth::guard('api')->user(); // Get authenticated user

    if (!$user) {
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    $shop = Shop::where('user_id', $user->id)->first();
    if (!$shop) {
        return response()->json(['error' => 'Shop not found'], 404);
    }

    // Get JWT token
    $jwtToken = request()->bearerToken();

    $url = "https://connect.stripe.com/oauth/authorize?" . http_build_query([
        'response_type' => 'code',
        'client_id' => config('services.stripe.client_id'),
        'scope' => 'read_write',
        'redirect_uri' => config('services.stripe.redirect_uri'),
        'state' => urlencode($jwtToken), // Attach JWT token to state parameter
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

