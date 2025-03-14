<?php

namespace App\Models;

use App\DB\Core\ImageField;
use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Electronic extends Model
{
    use HasFactory;

    protected $table = 'electronics';

    protected $fillable = [
        'name',
        'slug',
        'category_id',
        'brand_id',
        'description',
        'price',
        'discount',
        'stock_quantity',
        'warranty',
        'status',
    ];

    public function saveableFields($column): object
{
    $arr = [
        'name' => StringField::new(),
        'slug' => StringField::new(),
        'category_id' => IntegerField::new(),
        'brand_id' => IntegerField::new(),
        'description' => StringField::new(),
        'price' => IntegerField::new(),
        'discount' => IntegerField::new(),
        'stock_quantity' => IntegerField::new(),
        'warranty' => StringField::new(),
        'status' => StringField::new(),
        'upload_url' => ImageField::new(),
    ];

    if (!array_key_exists($column, $arr)) {
        throw CrudException::missingAttributeException();
    }

    return $arr[$column];
}

    /**
     * Get the category that owns the electronic item.
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the brand that owns the electronic item.
     */
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    /**
     * Polymorphic Relationship with Images.
     */
    public function images()
    {
        return $this->morphMany(Images::class, 'imageable');
    }

}
