<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class OrderItAgain extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $start_date = Carbon::now();
        $end_date = now()->subDays(7);
        $confirm_orders = Payment::query()->whereBetween('created_at', [$start_date, $end_date])->pluck('order-id');
    }
}
