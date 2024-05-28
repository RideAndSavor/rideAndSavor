<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Scout\Searchable;

class Ward extends Model
{
    use HasFactory, Searchable;

    public function saveableFields($column): object
    {
        $arr = [
            'name' => StringField::new(),
            'township_id' => IntegerField::new()
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

    public function township(): BelongsTo
    {
        return $this->belongsTo(Township::class);
    }

    public function street(): HasMany
    {
        return $this->hasMany(Street::class);
    }
}
