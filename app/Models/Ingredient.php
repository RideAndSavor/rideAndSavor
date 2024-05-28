<?php

namespace App\Models;

use App\Models\Food;
use App\DB\Core\StringField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ingredient extends Model
{
    use HasFactory;

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

    public function foods(): BelongsToMany
    {
        return $this->belongsToMany(Food::class, 'food_ingredient')->withTimestamps();;
    }
}
