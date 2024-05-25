<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\Exceptions\CrudException;
use App\Exceptions\CustomException;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Scout\Searchable;

class Country extends Model
{
    use HasFactory, Searchable;

    public function saveableFields($column): object
    {
        $arr = [
            'name' => StringField::new(),
            'slug' => StringField::new(),
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

    public function state(): HasMany
    {
        return $this->hasMany(State::class);
    }
}
