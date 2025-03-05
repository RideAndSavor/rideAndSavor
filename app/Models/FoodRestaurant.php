<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FoodRestaurant extends Pivot
{
    use HasFactory;

    protected $table = 'food_restaurant';

    protected $fillable = [
        'restaurant_id',
        'food_id',
        'price',
        'size_id',
        'discount_item_id',
        'description',
        'taste_id'
    ];

    public function saveableFields($column): object
    {
        // dd($column);
        $arr = [
            'restaurant_id' => IntegerField::new(),
            'size_id' => IntegerField::new(),
            'food_id' => IntegerField::new(),
            'discount_item_id' => IntegerField::new(),
            'price' => IntegerField::new(),
            'description' => StringField::new(),
            'taste_id' => IntegerField::new()
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }

    public function orderDetalis(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'food_id');
    }

    public $timestamps = true;

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    public function orderDetails()
    {
        return $this->hasMany(OrderDetail::class, 'food_restaurant_id');
    }

    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class, 'restaurant_id');
    }

    public function discount(): BelongsTo
    {
        return $this->belongsTo(DiscountItem::class, 'discount_item_id');
    }

    public function size(): BelongsTo
    {
        return $this->belongsTo(Size::class, 'size_id');
    }
    public function images()
    {
        return $this->morphMany(Images::class, 'imageable');
    }

}
