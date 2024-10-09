<?php

namespace App\Models;

use Exception;
use App\DB\Core\StringField;
use Laravel\Scout\Searchable;
use App\Exceptions\CrudException;
use App\Exceptions\CustomException;
use Illuminate\Database\Eloquent\Model;
use Staudenmeir\EloquentHasManyDeep\HasManyDeep;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory, Searchable;
    use \Staudenmeir\EloquentHasManyDeep\HasRelationships;

    public function saveableFields($column): object
    {
        $arr = [
            'name' => StringField::new(),
            'slug' => StringField::new(),
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

    public function state(): HasMany
    {
        return $this->hasMany(State::class);
    }

    public function townships(): HasManyDeep
    {
        return $this->hasManyDeep(Township::class, [State::class, City::class]);
    }
}
