<?php

// app/Models/ProductOrderDetail.php

namespace App\Models;

use App\DB\Core\DecimalField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductOrderDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_order_id',
        'product_id',
        'quantity',
        'original_price',
        'discount_price',
        'final_price',
        'shop_id'
    ];

    public function saveableFields($column): object
    {
        $arr = [
            'order_id' => IntegerField::new(),
            'product_id' => IntegerField::new(),
            'shop_id' => IntegerField::new(),
            'quantity' => IntegerField::new(),
            'original_price' => DecimalField::new(),
            'discount_price' => DecimalField::new(),
            'final_price' => DecimalField::new()
        ];

        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }

    public function productOrder()
    {
        return $this->belongsTo(ProductOrder::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}

