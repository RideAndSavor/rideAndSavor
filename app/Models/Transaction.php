<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\DecimalField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'amount',
        'currency',
        'payment_method',
        'status',
    ];

    public function saveableFields($column): object
{
    // Define the fields and their corresponding types
    $arr = [
        'order_id' => IntegerField::new(),
        'transaction_id' => StringField::new(), // Assuming transaction_id is a string (e.g., UUID)
        'amount' => DecimalField::new(), // Amount should be a decimal field for precision
        'currency' => StringField::new(), // Currency is usually a string (e.g., USD)
        'payment_method' => StringField::new(), // Payment method is usually a string (e.g., "card", "paypal")
        'status' => StringField::new(), // Status is usually a string (e.g., "pending", "completed")
    ];

    // Check if the provided column exists in the defined fields
    if (!array_key_exists($column, $arr)) {
        throw CrudException::missingAttributeException(); // Throw exception if the column is not found
    }

    // Return the field type for the requested column
    return $arr[$column];
}

    // Relationship with Order
    public function order()
    {
        return $this->belongsTo(ProductOrder::class, 'order_id');
    }


    public function images()
    {
        return $this->morphMany(Images::class, 'imageable');
    }
}
