<?php

namespace App\Models;

use App\DB\Core\IntegerField;
use App\DB\Core\StringField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class State extends Model
{
    use HasFactory;


    public function saveableFields(): array
    {
        return [
            'name' => StringField::new(),
            'country_id' => IntegerField::new()
        ];
    }
    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }

    public function city(): HasMany
    {
        return $this->hasMany(City::class);
    }
}
