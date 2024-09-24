<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\NearbyScope;
class TaxiDriver extends Model
{
    use HasFactory;
    
    
    use NearbyScope;

    protected $fillable = ['user_id', 'current_location', 'is_available'];
    
    protected $casts = [
        'current_location' => 'json',
    ];

}
