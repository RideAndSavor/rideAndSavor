<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class NearbyTaxi extends Model
{
    use HasFactory;

    protected $table = 'nearby_taxi';

    protected $fillable = [
        'travel_id',
        'taxi_driver_id',
        'driver_name',
        'plate_number'
    ];

    // Saveable fields method for validation
    public function saveableFields($column): object
    {
        $arr = [
            'travel_id' => IntegerField::new(),
            'taxi_driver_id' => IntegerField::new(),
            'driver_name' => StringField::new(),
            'plate_number' => StringField::new(),
        ];

        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }

    public function travel()
    {
        return $this->belongsTo(Travel::class, 'travel_id');
    }

    public function taxiDriver()
    {
        return $this->belongsTo(TaxiDriver::class, 'taxi_driver_id');
    }
}
