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

    public function saveableFields($column): object
    {
        $arr = [
            'link_id' => IntegerField::new(),
            'gener' => StringField::new(),
            'upload_url'=>ImageField::new(),
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }

    public function food():BelongsTo
    {
        return $this->belongsTo(Food::class,'link_id');
    }
}