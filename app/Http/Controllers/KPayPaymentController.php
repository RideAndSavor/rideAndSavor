<?php

namespace App\Http\Controllers;

use App\Models\KpayPaymentAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentSuccessMail;

class KPayPaymentController extends Controller
{
    public function processKpayPayment($order, $shop)
    {
        // Retrieve the shop's KBZPay account ID
        $shopKpayAccount = KpayPaymentAccount::where('shop_id', $shop->id)->first();

        if (!$shopKpayAccount || !$shopKpayAccount->kpay_no) {
            return response()->json(['error' => 'Shop does not have a connected KBZPay account'], 400);
        }

        try {
            // KBZPay API call (replace with actual KBZPay API call)
            $kpayResponse = $this->initiateKpayTransaction(
                $order->final_price,
                $shopKpayAccount->kpay_phone_number,
                $order->id
            );

            if ($kpayResponse && isset($kpayResponse['transactionId'])) {
                // Store transaction
                $transaction = Transaction::create([
                    'order_id' => $order->id,
                    'transaction_id' => $kpayResponse['transactionId'],
                    'amount' => $order->final_price,
                    'currency' => 'MMK',
                    'payment_method' => 'kpay',
                    'status' => 'succeeded',
                ]);

                // Update order status to "Paid"
                $order->update(['status_id' => 2]);

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

    private function initiateKpayTransaction($amount, $phoneNumber, $orderId)
    {
        return ['transactionId' => 'kpay_txn_' . time()];
    }
}
