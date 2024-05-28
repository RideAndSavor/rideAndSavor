<?php

namespace App\Models;

use App\Models\Images;
use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class Food extends Model
{
    use HasFactory, Searchable;

    public function saveableFields($column): object
    {
        $arr = [
            'name' => StringField::new(),
            'quantity' => StringField::new(),
            'sub_category_id' => IntegerField::new(),
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }

    public function toSearchableArray()
    {
        return [
            'name' => $this->name,
        ];
    }

    public function subCategory():BelongsTo
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

    public function ingredients(): BelongsToMany
    {
        return $this->belongsToMany(Ingredient::class, 'food_ingredient')->withTimestamps();
    }

    public function image(): HasOne
    {
        return $this->hasOne(Images::class, 'link_id');
    }
}
