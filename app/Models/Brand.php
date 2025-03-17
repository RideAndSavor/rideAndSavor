<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\DB\Core\StringField;
use App\Exceptions\CrudException;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'slug', 'status'];


    public function saveableFields($column): object
    {
        $arr = [
            'name' => StringField::new(),
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }

    // Automatically generate a slug when setting the name
    public static function boot()
    {
        parent::boot();

        static::creating(function ($brand) {
            $brand->slug = Str::slug($brand->name);
        });
    }

    public function electronics()
    {
        return $this->hasMany(Electronic::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}

