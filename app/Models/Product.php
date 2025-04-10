<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\DB\Core\StringField;
use App\DB\Core\DecimalField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'shop_id',
        'subcategory_id',
        'brand_id',
        'original_price',
        'discount_percent',
        'final_price',
        'stock_quantity',
        'weight',
        'color',
        'description',
        'status',
    ];

    public function saveableFields($column): object
    {
        $arr = [
            'name' => StringField::new(),
            'slug' => StringField::new(),
            'shop_id' => IntegerField::new(),
            'subcategory_id' => IntegerField::new(),
            'brand_id' => IntegerField::new(),
            'original_price' => DecimalField::new(),
            'discount_percent' => DecimalField::new(),
            'final_price' => DecimalField::new(),
            'stock_quantity' => IntegerField::new(),
            'weight' => DecimalField::new(),
            'color' => StringField::new(),
            'description' => StringField::new(),
            'status' => StringField::new(),
        ];

        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            $product->slug = Str::slug($product->name . '-' . $product->shop_id);
        });

        static::updating(function ($product) {
            $product->slug = Str::slug($product->name . '-' . $product->shop_id);
        });
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function subcategory()
    {
        return $this->belongsTo(Subcategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function images()
    {
        return $this->morphMany(Images::class, 'imageable');
    }
}
