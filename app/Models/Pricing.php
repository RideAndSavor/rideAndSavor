<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'base_price', 'discount_percent', 'final_price'];

    /**
     * Set final price automatically when setting base_price or discount_price.
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($pricing) {
            $pricing->final_price = $pricing->base_price - $pricing->discount_percent;
        });
    }

    /**
     * Relationship with Product Model.
     */
    public function product()
    {
        return $this->belongsTo(Electronic::class);
    }
}
