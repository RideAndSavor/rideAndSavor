<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Electronic extends Model
{
    use HasFactory;

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
    public function images(): MorphMany
    {
        return $this->morphMany(Images::class, 'imageable');
    }
}
