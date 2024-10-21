<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\DecimalField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cart extends Model
{
    use HasFactory;

    public function saveableFields($column): object
    {
        $arr = [
            'user_id' => IntegerField::new(),
            'total_price' => DecimalField::new(),
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }

    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItems::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
