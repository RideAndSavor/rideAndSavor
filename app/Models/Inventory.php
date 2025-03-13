<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Inventory extends Model
{
    use HasFactory;

    protected $table = 'inventories';

    protected $fillable = [
        'electronic_id',
        'in_stock',
        'out_stock',
        'remaining_stock',
        'stock_status',
        'remarks'
    ];

    public function saveableFields($column): object
    {
        $arr = [
            'electronic_id' => IntegerField::new(),
            'in_stock' => IntegerField::new(),
            'out_stock' => IntegerField::new(),
            'remaining_stock' => IntegerField::new(),
            'stock_status' => StringField::new(),
            'remarks' => StringField::new()
        ];

        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }

    /**
     * Relationship with Electronic Items
     */
    public function electronic(): BelongsTo  
    {
        return $this->belongsTo(Electronic::class, 'electronic_id');
    }

    /**
     * Relationship with Order Details
     */
    public function orderDetails(): HasMany
    {
        return $this->hasMany(OrderDetail::class, 'inventory_id');
    }

    /**
     * Relationship with Images (Polymorphic)
     */
    public function images(): MorphMany
    {
        return $this->morphMany(Images::class, 'imageable');
    }

    public $timestamps = true;
}
