<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use Laravel\Scout\Searchable;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Township extends Model
{
    use HasFactory, Searchable;

    public function saveableFields($column): object
    {
        $arr = [
            'name' => StringField::new(),
            'city_id' => IntegerField::new()
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

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }

    public function ward(): HasMany
    {
        return $this->hasMany(Ward::class);
    }

}
