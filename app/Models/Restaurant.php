<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use App\DB\Core\DateTimeField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Restaurant extends Model
{
    use HasFactory;
    public function saveableFields($column): object
    {
        $arr = [
            'address_id' => IntegerField::new(),
            'name' => StringField::new(),
            'open_time'=>DateTimeField::new(),
            'close_time'=>DateTimeField::new(),
            'phone_number'=>StringField::new()
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }

    public function address():BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

}