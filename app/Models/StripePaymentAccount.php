<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StripePaymentAccount extends Model
{
    use HasFactory;

    protected $table = 'stripe_payment_accounts';  // Change table name here

    protected $fillable = ['shop_id', 'stripe_account_id'];

    public function saveableFields($column): object
    {
        $arr = [
            'shop_id' => IntegerField::new(),
            'stripe_account_id' => StringField::new(),
        ];

        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
