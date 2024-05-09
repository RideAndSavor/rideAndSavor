<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Food extends Model
{
    use HasFactory;

    public function saveableFields(): array
    {
        return [
            'name' => StringField::new(),
            'quantity' => StringField::new(),
            'subCategory_id'=>IntegerField::new(),
        ];
    }

    public function subCategory():BelongsTo
    {
        return $this->belongsTo(SubCategory::class);
    }
}
