<?php

namespace App\Models;

use App\DB\Core\StringField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    public function saveableFields(): array
    {
        return [
            'name' => StringField::new(),
        ];
    }
}
