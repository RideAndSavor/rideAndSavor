<?php

namespace App\Repositories;

use App\Contracts\InvoiceInterface;
use App\Models\ProductOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class InvoiceRepository extends BaseRepository implements InvoiceInterface
{
    public function __construct()
    {
        parent::__construct(class_basename(ProductOrder::class));
    }

    public function generateMonthlyInvoices(int $year, int $month, int $userId)
    {
        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        return $this->currentModel::where('user_id', $userId)
            ->where('status_id', 3)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('orderDetails.product', 'user')
            ->get();
    }
}
