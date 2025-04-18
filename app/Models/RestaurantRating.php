<?php

namespace App\Models;

use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RestaurantRating extends Model
{
    use HasFactory;

    public function saveableFields($column): object
    {
        $arr = [
            'user_id' => IntegerField::new(),
            'restaurant_id' => IntegerField::new(),
            'rating_id' => IntegerField::new(),
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }
    public function restaurant(): BelongsTo
    {
        return $this->belongsTo(Restaurant::class);
    }
}
