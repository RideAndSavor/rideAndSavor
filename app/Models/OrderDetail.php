<?php

namespace App\Models;

use App\DB\Core\DecimalField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class OrderDetail extends Model
{
    use HasFactory;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;


    public function saveableFields($column): object
    {
        $arr = [
            'order_id' => IntegerField::new(),
            'food_restaurant_id' => IntegerField::new(),
            'quantity' => IntegerField::new(),
            'price' => DecimalField::new(),
            'discount_prices' => DecimalField::new(),
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function foodRestaurant()
    {
        return $this->belongsTo(FoodRestaurant::class, 'food_restaurant_id');
    }
}
