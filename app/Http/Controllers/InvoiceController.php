<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\ProductOrder;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    /**
     * Generate invoice PDF for a confirmed order.
     *
     * @param int $orderId
     * @return \Illuminate\Http\Response
     */
    public function generateInvoice($orderId)
    {
        // 1️⃣ Retrieve the confirmed order
        $order = ProductOrder::where('id', $orderId)
                        // ->where('shop_id', Auth::user()->shop->id)
                      ->where('status_id', 3)
                      ->with('orderDetails.product','user') // Load related order details and products
                      ->first();

        // $order = ProductOrder::where('id', $orderId)
        // ->where('shop_id', Auth::user()->shop->id)
        // ->where('status_id', 3) // Only confirmed orders
        // ->with('orderDetails') // Load related details
        // ->first();

        // 2️⃣ Check if order exists and is confirmed
        if (!$order) {
            return response()->json(['message' => 'Order not found or not confirmed'], 404);
        }

        // 3️⃣ Prepare order data for PDF
        $data = [
            'order' => $order,
            'orderDetails' => $order->orderDetails,
            'finalAmount' => number_format($order->final_price, 2)
        ];

        // 4️⃣ Generate PDF
        $pdf = Pdf::loadView('invoice', $data);

        // 5️⃣ Return PDF as download response
        return $pdf->download("invoice_order_{$orderId}.pdf");
    }
}
