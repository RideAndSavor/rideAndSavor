<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Traits\CanLoadRelationships;

class OrderItAgain extends Controller
{
    use CanLoadRelationships;
    private array $relations = [
        'orderDetalis',
        'orderDetalis.foodRestaurant',
        'orderDetalis.foodRestaurant.food',
        'orderDetalis.foodRestaurant.food.foodViewImages',
    ];
    public function __invoke(Request $request)
    {
        $start_date = now()->subDays(7);
        $end_date = now();
        // $confirm_orders = Order::query()->with('orderDetalis.foodRestaurant.food', function ($q) {
        //     $q->select('id');
        // })->where('status_id', 3)->whereBetween('created_at', [$start_date, $end_date])->get();
        $query = $this->loadRelationships(Order::query()->where('status_id', 3)->whereBetween('created_at', [$start_date, $end_date]));
        // dd(Order::with('orderDetalis.foodRestaurant.food.foodViewImages')->get());
        dd($query->get());
    }
}

