<?php

namespace App\Models;

use App\DB\Core\ImageField;
use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Images extends Model
{
    use HasFactory;
    protected $guarded = [];
    // protected $fillable = [ 'genre', 'model_id', 'model_type'];

    public function saveableFields($column): object
    {
        $arr = [
            'link_id' => IntegerField::new(),
            'gener' => StringField::new(),
            'upload_url'=> ImageField::new(),
        ];

        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }

    public function imageable()
    {
        return $this->morphTo();
    }

    public function food(): BelongsTo
    {
        return $this->belongsTo(Food::class, 'link_id');
    }

    public function electronic(): BelongsTo
    {
        return $this->belongsTo(Electronic::class, 'link_id');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class, 'link_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'link_id');
    }
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class, 'link_id');
    }
}
