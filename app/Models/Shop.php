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
            'social_media_links' => StringField::new(), // This might need a JSON field handling
            'open_time' => StringField::new(),
            'close_time' => StringField::new(),
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
    }

    // Define the relationship with the Address model
    public function address()
    {
        return $this->belongsTo(Address::class);
    }
}
