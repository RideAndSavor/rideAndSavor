<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentProvider extends Model
{
    use HasFactory;

    public function saveableFields($column): object
    {
        $arr = [
            'paymentmode' => StringField::new()
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }
}
