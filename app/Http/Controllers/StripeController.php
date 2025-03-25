<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Account;
use App\Models\Shop;
use App\Models\StripePaymentAccount;
use Illuminate\Support\Facades\Auth;

class StripeController extends Controller
{
    /**
     * Redirect shop owner to Stripe for account creation
     */
    public function createAccount(Request $request)
    {
        $shop = Shop::where('id', $request->shop_id)->first();

        if (!$shop) {
            return response()->json(['error' => 'Shop not found'], 404);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        // Create Stripe Account for the shop
        $account = Account::create([
            'type' => 'express',
            'country' => 'US', // Change this based on your country
            'email' => $shop->email,
            'capabilities' => [
                'card_payments' => ['requested' => true],
                'transfers' => ['requested' => true],
            ],
        ]);

        // Store Stripe account ID in 'stripe_payment_accounts' table
        StripePaymentAccount::updateOrCreate(
            ['shop_id' => $shop->id],
            ['stripe_account_id' => $account->id]
        );

        // Generate Stripe Account Link for onboarding
        $accountLink = \Stripe\AccountLink::create([
            'account' => $account->id,
            'refresh_url' => url('/stripe/reauth'),
            'return_url' => url('/stripe/success'),
            'type' => 'account_onboarding',
        ]);

        return response()->json(['url' => $accountLink->url]);
    }

    /**
     * Retrieve Shop Stripe Account Info
     */
    public function getAccountInfo(Request $request)
    {
        $shop = Shop::where('id', $request->shop_id)->with('stripePaymentAccount')->first();

        if (!$shop || !$shop->stripePaymentAccount || !$shop->stripePaymentAccount->stripe_account_id) {
            return response()->json(['error' => 'Shop Stripe account not found'], 404);
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        $account = Account::retrieve($shop->stripePaymentAccount->stripe_account_id);

        return response()->json(['stripe_account' => $account]);
    }
}

