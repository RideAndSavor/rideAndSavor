<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FoodImage extends Model
{
    protected $table = 'vw_food_images';

    public function food()
    {
        return $this->belongsTo(Food::class);
    }

    protected $hidden = [
        'link_id',
        'gener',
    ];
}
