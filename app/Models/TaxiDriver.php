<?php

namespace App\Models;

use App\DB\Core\IntegerField;
use App\DB\Core\StringField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\NearByScope;
use Doctrine\DBAL\Types\IntegerType;

class TaxiDriver extends Model
{
    use HasFactory;
    
    
    use NearByScope;

    // protected $fillable = ['user_id', 'current_location', 'is_available'];
    
    protected $fillable = [
        // 'rider_id',
        'current_location',
        'is_available',
        'car_year',
        'car_make',
        'car_model',
        'car_colour',
        'license_plate',
        'other_info',
    ];

    public function saveableFields($column): object
    {
        $arr = [
            'user_id' => IntegerField::new(),
            'current_location'  => StringField::new(),
            'is_available' => IntegerField::new(),
            'car_year' => IntegerField::new(),
            'car_make' => StringField::new(),
            'car_model' => StringField::new(),
            'car_colour' => StringField::new(),
            'license_plate' => StringField::new(),
            'other_info' => StringField::new()
        ];
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }
    
    protected $casts = [
        'current_location' => 'json',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

}
