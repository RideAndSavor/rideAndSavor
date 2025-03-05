<?php

namespace App\Models;

use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BiddingPriceByDriver extends Model
{
    use HasFactory;
    protected $table = 'bidding_prices';

    protected $fillable = ['travel_id', 'taxi_driver_id', 'price'];

    public function saveableFields($column): object
    {
        // dd($column);
        $arr = [
            'travel_id' => IntegerField::new(),
            'taxi_driver_id' => IntegerField::new(),
            'price' => IntegerField::new(), // Assuming price can have decimals
        ];
        // dd($arr);

        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }

    public function travel()
    {
        return $this->belongsTo(Travel::class, 'travel_id'); // 'travel_id' as the foreign key
    }

    public function driver()
    {
        return $this->belongsTo(TaxiDriver::class, 'taxi_driver_id'); // 'taxi_driver_id' as the foreign key
    }

}
