<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Street extends Model
{
    use HasFactory;
    public function saveableFields($column): object
    {
        $arr = [
            'name' => StringField::new(),
            'ward_id' => IntegerField::new()
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }



    public function ward(): BelongsTo
    {
        return $this->belongsTo(Ward::class);
    }

    public function address()
    {
        return $this->hasMany(Address::class);
    }
}
