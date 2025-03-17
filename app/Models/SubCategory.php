<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use Laravel\Scout\Searchable;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SubCategory extends Model
{
    use HasFactory, Searchable;

    public function saveableFields($column): object
    {
        $arr = [
            'name' => StringField::new(),
            'category_id' => IntegerField::new()
        ];
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

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function foods(): HasMany
    {
        return $this->hasMany(Food::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
