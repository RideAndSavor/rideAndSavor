<?php

namespace App\Models;

use App\DB\Core\StringField;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;


     protected $fillable =['name'];

     public function saveableFields(): array
     {
         return [
             'name' => StringField::new(),
         ];
     }
}

