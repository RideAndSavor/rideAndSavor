<?php

namespace App\Models;

use App\DB\Core\IntegerField;
use App\DB\Core\StringField;
use App\DB\Core\DoubleField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

use App\Traits\NearByScope;
use Doctrine\DBAL\Types\IntegerType;

class TaxiDriver extends Model
{
    use HasFactory, Notifiable;
    use NearByScope;

    protected $table = 'taxi_drivers'; // if different from default naming convention

    // Define the attributes for latitude and longitude
    protected $fillable = [
        'user_id', 'latitude', 'longitude', 'is_available',
        'car_year', 'car_make', 'car_model', 'car_colour',
        'license_plate', 'driver_license_number', 'other_info'
    ];

    // Method to define saveable fields (for CRUD operations)
    public function saveableFields($column): object
    {

        $arr = [
            'user_id' => IntegerField::new(),
            'latitude' => DoubleField::new(), // Latitude as Integer or Float
            'longitude' => DoubleField::new(), // Longitude as Integer or Float
            'is_available' => IntegerField::new(),
            'car_year' => IntegerField::new(),
            'car_make' => StringField::new(),
            'car_model' => StringField::new(),
            'car_colour' => StringField::new(),
            'license_plate' => StringField::new(),
            'driver_license_number' => StringField::new(),
            'other_info' => StringField::new(),
        ];

        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return  $arr[$column];
    }

    // Define the casting for the attributes
    protected $casts = [
        // If latitude and longitude are stored as floats or integers, no casting is needed, otherwise use 'float' or 'decimal'
        'latitude' => 'float',
        'longitude' => 'float',
    ];

    // Define the relationship with the User model
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

     // Define the relationship with BiddingPriceByDriver model
     public function biddingPriceByDrivers()
     {
         return $this->hasMany(BiddingPriceByDriver::class, 'taxi_driver_id');
     }
     public function nearbyTaxis()
    {
        return $this->hasMany(NearbyTaxi::class, 'taxi_driver_id');
    }


}
