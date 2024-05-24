<?php

namespace App\Models;

use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DeliveryPrice extends Model
{
    use HasFactory;

    public function saveableFields($column): object
    {
        $arr = [
            'township_id' => IntegerField::new(),
            'price_id' => IntegerField::new()
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }

    public function township(): BelongsTo
    {
        return $this->belongsTo(Township::class);
    }

    public function price(): BelongsTo
    {
        return $this->belongsTo(Price::class);
    }
}
