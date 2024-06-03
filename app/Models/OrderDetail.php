<?php

namespace App\Models;

use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderDetail extends Model
{
    use HasFactory;

    public function saveableFields($column): object
    {
        $arr = [
            'order_id' => IntegerField::new(),
            'food_restaurant_id' => IntegerField::new(),
            'quantity' => IntegerField::new(),
            'discount_prices' => IntegerField::new(),
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function foodRestaurant()
    {
        return $this->belongsTo(FoodRestaurant::class);
    }
}
