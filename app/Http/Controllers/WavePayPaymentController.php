<?php

namespace App\Http\Controllers;

use App\Models\WavepayPaymentAccount;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Exception;
use Illuminate\Support\Facades\Mail;
use App\Mail\PaymentSuccessMail;

class WavePayPaymentController extends Controller
{
    public function processWavepayPayment($order, $shop)
    {
        // Retrieve the shop's WavePay account ID
        $shopWavepayAccount = WavepayPaymentAccount::where('shop_id', $shop->id)->first();

        if (!$shopWavepayAccount || !$shopWavepayAccount->wavepay_no) {
            return response()->json(['error' => 'Shop does not have a connected WavePay account'], 400);
        }

        try {
            // WavePay API call (replace with actual WavePay API call)
            $wavepayResponse = $this->initiateWavepayTransaction(
                $order->final_price,
                $shopWavepayAccount->wavepay_no,
                $order->id
            );

            if ($wavepayResponse && isset($wavepayResponse['transactionId'])) {
                // Store transaction
                $transaction = Transaction::create([
                    'order_id' => $order->id,
                    'transaction_id' => $wavepayResponse['transactionId'],
                    'amount' => $order->final_price,
                    'currency' => 'MMK',
                    'payment_method' => 'wavepay',
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

    private function initiateWavepayTransaction($amount, $phoneNumber, $orderId)
    {
        return ['transactionId' => 'wavepay_txn_' . time()];
    }
}
