<?php

namespace App\Models;

use App\DB\Core\IntegerField;
use App\DB\Core\StringField;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\Exceptions\CrudException;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address_id',
        'slug',
        'description',
        'website_url',
        'phone_number',
        'email',
        'social_media_links',
        'open_time',
        'close_time',
        'status',
        'user_id',
        'discount_id',
    ];

    protected $casts = [
        'social_media_links' => 'array',
        'open_time' => 'string',
        'close_time' => 'string',
    ];

    public function saveableFields($column): object
    {
        $arr = [
            'name' => StringField::new(),
            'address_id' => IntegerField::new(),
            'description' => StringField::new(),
            'website_url' => StringField::new(),
            'phone_number' => StringField::new(),
            'email' => StringField::new(),
            'status' => StringField::new(),
            'social_media_links' => StringField::new(),
            'open_time' => StringField::new(),
            'close_time' => StringField::new(),
            'user_id' => IntegerField::new(),
            'discount_id' => IntegerField::new(),
            'slug' => StringField::new(),
        ];

        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function ($shop) {
            $shop->slug = Str::slug($shop->name);
        });

        static::updating(function ($shop) {
            $shop->slug = Str::slug($shop->name);
        });
    }

    // Define the relationship with the Address model
    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'shop_category');
    }

    /**
     * Polymorphic Relationship with Images.
     */
    public function images()
    {
        return $this->morphMany(Images::class, 'imageable');
    }

    public function stripePaymentAccount()
    {
        return $this->hasOne(StripePaymentAccount::class, 'shop_id', 'id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function discountItem()
    {
        return $this->belongsTo(\App\Models\DiscountItem::class, 'discount_id');
    }

}
