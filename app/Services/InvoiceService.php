<?php

namespace App\Services;

use App\Contracts\InvoiceInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\{File, Storage};
use ZipArchive;

class InvoiceService
{
    public function __construct(private InvoiceInterface $invoiceRepo) {}

    public function getById(int $id, array $relations = [])
    {
        return $this->invoiceRepo->getById($id, $relations);
    }

    public function handleMonthlyInvoices(int $year, int $month, int $userId): array
    {
        $orders = $this->invoiceRepo->generateMonthlyInvoices($year, $month, $userId);

        return $orders->isEmpty()
            ? throw new \Exception('No orders found')
            : $this->createZipArchive($year, $month, $orders);
    }

    private function createZipArchive(int $year, int $month, $orders): array
    {
        $zipPath = $this->generateZip($year, $month, $orders);
        $publicPath = public_path("storage/".basename($zipPath));

        File::ensureDirectoryExists(dirname($publicPath));

        return [
            'success' => "zip file created successfully for your orders within {$year}-{$month}",
            'download_url' => $publicPath,
        ];
    }

    private function generateZip(int $year, int $month, $orders): string
    {
        $zip = new ZipArchive;
        $path = storage_path("app/public/invoices_{$year}_{$month}.zip");
        File::ensureDirectoryExists(dirname($path));

        if ($zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException('Failed to create ZIP');
        }

        $orders->each(fn($order) => $zip->addFromString(
            "invoice_{$order->id}.pdf",
            Pdf::loadView('invoice', [
                'order' => $order,
                'orderDetails' => $order->orderDetails,
                'finalAmount' => number_format($order->final_price, 2)
            ])->output()
        ));
        $zip->close();
        return $path;
    }
}
