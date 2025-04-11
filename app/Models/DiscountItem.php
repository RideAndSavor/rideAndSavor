<?php

namespace App\Models;

use App\DB\Core\DateTimeField;
use App\DB\Core\IntegerField;
use App\DB\Core\StringField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;

class DiscountItem extends Model
{
    use HasFactory, Searchable;

    protected $fillable = [
        'percentage_id',
        'name',
        'start_date',
        'end_date',
    ];

    public function saveableFields($column): object
    {
        $arr = [
            'percentage_id' => IntegerField::new(),
            'name' => StringField::new(),
            'start_date' => DateTimeField::new(),
            'end_date' => DateTimeField::new()
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }

    public function toSearchableArray()
    {
        return [
            "name" => $this->name,
        ];
    }

    public function percentage()
    {
        return $this->belongsTo(Percentage::class);
    }

    public function shop()
    {
        return $this->hasOne(\App\Models\Shop::class, 'discount_id');
    }
}
