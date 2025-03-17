<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Category extends Model
{
    use HasFactory, Searchable;

    public function saveableFields($column): object
    {
        $arr = [
            'name' => StringField::new(),
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
    public function subCategory():HasMany
    {
        return $this->hasMany(SubCategory::class);
    }

    public function electronics()
    {
        return $this->hasMany(Electronic::class);
    }

    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'shop_category');
    }

}
