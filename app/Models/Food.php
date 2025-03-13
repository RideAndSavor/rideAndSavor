<?php

namespace App\Models;

use App\Models\Images;
use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use Laravel\Scout\Searchable;
use App\Exceptions\CrudException;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Food extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'name',
        'sub_category_id'
    ];

    public function saveableFields($column): object
    {
        // dd("OK");
        $arr = [
            'name' => StringField::new(),
            // 'quantity' => StringField::new(),
            'sub_category_id' => IntegerField::new(),
        ];
        // dd($arr);
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
        ];
    }

    public function scopeFavoriteCuisines(Builder $query): void
    {
        $query->whereHas(
            'foodRestaurants.orderDetails.order',
            fn($query) =>
            $query->where('status_id', config('variable.THREE'))
                ->where('user_id', auth()->id())

        )
            ->with('foodImages')
            ->join('food_restaurant', 'food_restaurant.food_id', '=', 'food.id')
            ->join('order_details', 'order_details.food_restaurant_id', '=', 'food_restaurant.id')
            ->select('food.id', 'food.name', DB::raw('SUM(order_details.quantity) as food_count')) // Sum the quantity ordered
            ->groupBy('food.id', 'food.name');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }

    public function restaurants()
    {
        return $this->belongsToMany(Restaurant::class, 'food_restaurant')
            ->using(FoodRestaurant::class)
            ->withPivot('price', 'size_id', 'discount_item_id')
            ->withTimestamps();
    }

    public function toppings(): BelongsToMany
    {
        return $this->belongsToMany(Topping::class, 'food_toppings')->withTimestamps();
    }

    public function image(): HasMany
    {
        return $this->hasMany(Images::class, 'link_id');
    }

    public function foodImages(): HasMany
    {
        return $this->hasMany(FoodImage::class);
    }

    public function foodRestaurants()
    {
        return $this->hasMany(FoodRestaurant::class, 'restaurant_id', 'id');
    }

    public function orderDetails()
    {
        return $this->hasManyThrough(
            OrderDetail::class,
            FoodRestaurant::class,
            'food_id',
            'food_restaurant_id',
            'id', //(current model key): The local key on the current model.
            'id' // (intermediate model key): The local key on the FoodRestaurant model.
        );
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function restaurantss(): HasMany
    {
        return $this->hasMany(Restaurant::class);
    }

    public function images(): MorphMany
    {
        return $this->morphMany(Images::class, 'imageable');
    }

}
