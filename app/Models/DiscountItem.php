<?php

namespace App\Models;

use App\DB\Core\DateTimeField;
use App\DB\Core\IntegerField;
use App\DB\Core\StringField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiscountItem extends Model
{
    use HasFactory;

    public function saveableFields($column): object
    {
        $arr = [
            'percentage_id' => IntegerField::new(),
            'name' => StringField::new(),
            'start_date' => DateTimeField::new(),
            'end_date' => DateTimeField::new()
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }

    public function percentage()
    {
        return $this->belongsTo(Percentage::class);
    }
}
