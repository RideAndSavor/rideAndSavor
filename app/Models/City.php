<?php

namespace App\Models;

use App\DB\Core\IntegerField;
use App\DB\Core\StringField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class City extends Model
{
    use HasFactory;


    public function saveableFields(): array
    {
        return [
            'name' => StringField::new(),
            'state_id'=>IntegerField::new()
        ];
    }

    public function state():BelongsTo
    {
        return $this->belongsTo(State::class);
    }

}
