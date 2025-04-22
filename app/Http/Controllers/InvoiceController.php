<?php

namespace App\Http\Controllers;

use ZipArchive;
use App\Models\ProductOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;


class InvoiceController extends BaseController
{
    protected $invoiceService;

    public function __construct(InvoiceService $invoiceService) {
        $this->invoiceService = $invoiceService;
    }

    public function generateInvoiceForEachOrder($orderId)
    {
        return $this->handleRequest(function() use ($orderId) {
            $order = $this->invoiceService->getById($orderId, ['orderDetails.product', 'user']);

            if ($order->status_id !== 3) {
                throw new \Exception('Order not confirmed');
            }

            return Pdf::loadView('invoice', [
                'order' => $order,
                'orderDetails' => $order->orderDetails,
                'finalAmount' => number_format($order->final_price, 2)
            ])->download("invoice_order_{$orderId}.pdf");
        });
    }

    public function generateMonthlyInvoices($year, $month)
    {
        return $this->handleRequest(function() use ($year, $month) {
            $userId = Auth::user()->id;
            $generateZip= $this->invoiceService->handleMonthlyInvoices((int)$year, (int)$month,$userId);
            return response()->json($generateZip);
        });
    }

}
