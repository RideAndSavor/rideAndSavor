<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubCategory extends Model
{
    use HasFactory;

    public function saveableFields($column): object
    {
        $arr = [
            'name' => StringField::new(),
            'category_id'=>IntegerField::new()
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }

    public function category():BelongsTo
    {
       return $this->belongsTo(Category::class);
    }

    public function foods():HasMany
    {
        return $this->hasMany(Food::class);
    }
}
