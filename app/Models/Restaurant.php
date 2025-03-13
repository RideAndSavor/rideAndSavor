<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use Laravel\Scout\Searchable;
use App\DB\Core\DateTimeField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Restaurant extends Model
{
    use HasFactory, Searchable;
    public function saveableFields($column): object
    {
        $arr = [
            'address_id' => IntegerField::new(),
            'restaurant_type_id' => IntegerField::new(),
            'user_id' => IntegerField::new(),
            'name' => StringField::new(),
            'open_time' => DateTimeField::new(),
            'close_time' => DateTimeField::new(),
            'phone_number' => StringField::new()
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
            "name" => $this->name,
        ];
    }

    public function scopeMostFoodOrderRestaurantsByUser(Builder $query, string $start_date, string $end_date): void
    {
        $query->with(
            [
                'foodRestaurants.orderDetails' => fn($query) =>
                    $query->whereHas(
                        'order',
                        fn($query) =>
                        $query->where('status_id', config('variable.THREE'))
                            ->where('user_id', auth()->id())
                            ->whereBetween('created_at', [$start_date, $end_date])
                    )
            ]
        );
        // ->with(['ratings', 'comments', 'restaurantImages'])
        // ->withAvg('ratings', 'rating_id');
    }

    public function scopePopularRestaurants(Builder $query): void
    {
        $query->with(
            [
                'foodRestaurants.orderDetails' => fn($query) =>
                    $query->whereHas(
                        'order',
                        fn($query) =>
                        $query->where('status_id', config('variable.THREE'))
                    )
            ]
        );
    }

    public function scopeFeatureRestaurants(Builder $query){
        $query->where('feature_status',1);
    }

    // polymorph relationship
    public function image(): MorphOne
    {
        return $this->morphOne(Images::class, 'imageable');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function foods()
    {
        return $this->belongsToMany(Food::class, 'food_restaurant')
            ->using(FoodRestaurant::class)
            ->withPivot('price', 'size_id', 'discount_item_id')
            ->withTimestamps();
    }

    public function foodRestaurants()
    {
        return $this->hasMany(FoodRestaurant::class, 'restaurant_id');
    }

    public function orderDetails()
    {
        return $this->hasManyThrough(OrderDetail::class, FoodRestaurant::class, 'restaurant_id', 'food_restaurant_id');
    }

    public function ratings(): HasMany
    {
        return $this->hasMany(RestaurantRating::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(RestaurantComment::class);
    }

    public function restaurantImages(): HasMany
    {
        return $this->hasMany(RestaurantImage::class);
    }

    public function restaurantType(): BelongsTo
    {
        return $this->belongsTo(RestaurantType::class);
    }
}
