<?php

namespace App\Models;

use App\DB\Core\IntegerField;
use App\DB\Core\StringField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Number;

class Percentage extends Model
{
    use HasFactory;

    public function discount(float $price, float $percent): float
    {
        return ($price - (($price * $percent) / 100));
    }

    public function saveableFields($column): object
    {
        $arr = [
            'discount_percentage' => IntegerField::new(),
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }

    public function discountItems()
    {
        return $this->hasMany(DiscountItem::class);
    }
}
