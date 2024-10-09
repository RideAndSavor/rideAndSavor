<?php

namespace App\Http\Controllers;

use App\Models\OrderDetail;
use App\Traits\CanLoadRelationships;
use App\Http\Resources\OrderDetailResource;

class OrderItAgain extends Controller
{
    use CanLoadRelationships;
    private array $relations = [
        'foodRestaurant',
        'foodRestaurant.food',
        'foodRestaurant.food.foodViewImages',
    ];
    public function __invoke()
    {
        $start_date = now()->subDays(7);
        $end_date = now();
        $data = OrderDetail::query()
            ->select('order_id', 'food_restaurant_id','discount_prices')
            ->whereHas('order', function ($query) use ($start_date, $end_date) {
                $query->select('id', 'user_id', 'created_at', 'status_id')
                    ->where('status_id', config('variable.THREE'))
                    ->whereBetween('created_at', [$start_date, $end_date]);
            })
            ->with(['foodRestaurant.food.foodViewImages'])
            ->get();

        return OrderDetailResource::collection($data);

    }
}

