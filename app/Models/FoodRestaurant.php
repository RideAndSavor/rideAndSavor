<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\Pivot;

class FoodRestaurant extends Pivot
{
    use HasFactory;

    protected $table = 'food_restaurant';

    protected $fillable = [
        'restaurant_id', 'food_id', 'price', 'size_id', 'discount_item_id'
    ];

    public function orderDetalis() : HasMany {
        return $this->hasMany(OrderDetail::class,'food_id');
    }

    public function food():BelongsTo
    {
        return $this->belongsTo(Food::class);
    }

    public $timestamps = true;
}
