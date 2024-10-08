<?php

namespace App\Models;

use App\Models\Food;
use App\DB\Core\StringField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Laravel\Scout\Searchable;

class Topping extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'name', 'price'
    ];
    public function saveableFields($column): object
    {
        $arr = [
            'name' => StringField::new(),
            'price' => StringField::new(),
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }

    public function toSearchableArray()
    {
        return [
            "name" => $this->name,
        ];
    }

    public function foods(): BelongsToMany
    {
        return $this->belongsToMany(Food::class, 'food_toppings')->withTimestamps();;
    }
}
