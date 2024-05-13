<?php

namespace App\Models;

use App\DB\Core\DateTimeField;
use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Restaurant extends Model
{
    use HasFactory;

    public function saveableFields(): array
    {
        return [
            'address_id' => IntegerField::new(),
            'name' => StringField::new(),
            'open_time'=>DateTimeField::new(),
            'close_time'=>DateTimeField::new(),
            'phone_number'=>StringField::new()
        ];
    }

    public function address():BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

}
