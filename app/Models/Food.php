<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Food extends Model
{
    use HasFactory;

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
}
