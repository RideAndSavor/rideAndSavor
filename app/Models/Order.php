<?php

namespace App\Models;

use App\DB\Core\DecimalField;
use App\DB\Core\IntegerField;
use App\DB\Core\StringField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Order extends Model
{
    use HasFactory;

    public function saveableFields($column): object
    {
        $arr = [
            'user_id' => IntegerField::new(),
            'status_id' => IntegerField::new(),
            'delivery_price_id' => IntegerField::new(),
            'total_amount' => DecimalField::new(),
            'total_discount_amount' => DecimalField::new(),
            'comment' => StringField::new(),
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function delivery_price(): BelongsTo
    {
        return $this->belongsTo(DeliveryPrice::class);
    }
}