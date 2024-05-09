<?php

namespace App\Models;

use App\DB\Core\StringField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    use HasFactory;

     public function saveableFields(): array
     {
         return [
             'name' => StringField::new(),
         ];
     }

     public function state():HasMany
     {
        return $this->hasMany(State::class);
     }
}

