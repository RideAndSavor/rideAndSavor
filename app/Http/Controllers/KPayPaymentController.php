<?php

namespace App\Http\Controllers;

use Exception;
use App\Traits\ImageTrait;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Mail\PaymentSuccessMail;
use App\Models\KpayPaymentAccount;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class KPayPaymentController extends Controller
{
    use ImageTrait;
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }
    public function processKpayPayment(Request $request,$order, $shop)
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

                // ✅ Validate image upload
                $request->validate([
                    'upload_url' => 'required|image|mimes:jpeg,png,jpg|max:2048',
                ]);

                // ✅ Handle KPay screenshot upload
                if ($request->hasFile('upload_url')) {
                    $this->createImageTest($transaction, [[$request->file('upload_url')]], 'transaction_screenshots/', 'transaction');
                }

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
