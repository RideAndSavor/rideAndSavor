<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\DecimalField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_id',
        'status_id',
        'delivery_id',
        'total_price',
        'discount_price',
        'final_price',
        'comment'
    ];

    public function saveableFields($column): object
    {
        $arr = [
            'user_id' => IntegerField::new(),
            'shop_id' => IntegerField::new(),
            'status_id' => IntegerField::new(),
            'delivery_id' => IntegerField::new(),
            'total_price' => DecimalField::new(),
            'discount_price' => DecimalField::new(),
            'final_price' => DecimalField::new(),
            'comment' => StringField::new(),
        ];

        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function delivery()
    {
        return $this->belongsTo(DeliveryPrice::class, 'delivery_id');
    }

    public function payment()
    {
        return $this->hasMany(Transaction::class, 'order_id');
    }


    public function orderDetails()
    {
        return $this->hasMany(ProductOrderDetail::class, 'product_order_id'); // Correct foreign key
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class, 'shop_id');
    }
}

