<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class FoodRestaurant extends Pivot
{
    use HasFactory;

    protected $table = 'food_restaurant';

    protected $fillable = [
        'restaurant_id', 'food_id', 'price', 'size_id', 'discount_item_id'
    ];

    public $timestamps = true;
}
