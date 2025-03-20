<?php

namespace App\Models;

use App\DB\Core\DoubleField;
use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Travel extends Model
{
    use HasFactory;
    protected $table = 'travels';

    protected $fillable = [
        'user_id', 'pickup_latitude', 'pickup_longitude',
        'destination_latitude', 'destination_longitude', 'status'
    ];

    // Method to define saveable fields for Travel
    public function saveableFields($column): object
    {
        // dd($column);
        $arr = [
            'user_id' => IntegerField::new(),
            'pickup_latitude' => DoubleField::new(),
            'pickup_longitude' => DoubleField::new(),
            'destination_latitude' => DoubleField::new(),
            'destination_longitude' => DoubleField::new(),
            'status' => StringField::new(),
        ];
        // dd($arr);
        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }


    public function bids()
    {
        return $this->hasMany(BiddingPriceByDriver::class, 'travel_id');
    }

    public function acceptedDriver()
    {
        return $this->hasOne(AcceptDriver::class, 'travel_id');
    }
    public function nearbyTaxis()
    {
        return $this->hasMany(NearbyTaxi::class, 'travel_id');
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

