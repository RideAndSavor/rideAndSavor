<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Address extends Model
{
    use HasFactory;

    public function saveableFields(): array
    {
        return [
            'street_id'=>IntegerField::new(),
            'name' => StringField::new(),
            'block_no'=>StringField::new(),
            'floor'=>StringField::new(),
            'description'=>StringField::new(),
        ];
    }


}