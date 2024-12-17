<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Config;
use Laravel\Scout\Searchable;

class Address extends Model
{
    use HasFactory, Searchable;
    protected $guarded = ['id'];

    public function saveableFields($column): object
    {
        $arr = [
            'street_id' => IntegerField::new(),
            'block_no' => StringField::new(),
            'floor' => StringField::new(),
            'latitude' => StringField::new(),
            'longitude' => StringField::new()
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }

    public function toSearchableArray()
    {
        return [
            "block_no" => $this->block_no,
            "floor" => $this->floor,
        ];
    }
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function street()
    {
        return $this->belongsTo(Street::class);
    }

    public function restaurant(): HasOne
    {
        return $this->hasOne(Restaurant::class);
    }
}
