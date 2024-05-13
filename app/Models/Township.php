<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Township extends Model
{
    use HasFactory;

    public function saveableFields(): array
    {
        return [
            'name' => StringField::new(),
            'city_id' => IntegerField::new()
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
