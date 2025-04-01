<?php

namespace App\Models;

use App\DB\Core\StringField;
use App\DB\Core\IntegerField;
use App\Exceptions\CrudException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AcceptDriver extends Model
{
    use HasFactory;
    protected $table = 'accepted_drivers';
    protected $fillable = ['user_id', 'taxi_driver_id', 'travel_id', 'price', 'status'];

    public function saveableFields($column): object
    {
        $arr = [
            'user_id' => IntegerField::new(),
            'taxi_driver_id' => IntegerField::new(),
            'travel_id' => IntegerField::new(),
            'price' => IntegerField::new(), // Change to DecimalField if needed
            'status' => StringField::new()
        ];

        if (!array_key_exists($column, $arr)) {
            throw CrudException::missingAttributeException();
        }

        return $arr[$column];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function driver()
    {
        return $this->belongsTo(User::class, 'taxi_driver_id');
    }

    public function travel()
    {
        return $this->belongsTo(Travel::class);
    }
}



