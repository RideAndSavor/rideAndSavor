<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trip extends Model
{
    use HasFactory;

    protected $fillable = [
        'rider_id',
        'selected_driver_id',
        'status',
        'current_location',
        'destination',
        'driver_location',
        'price',
    ];

    protected $casts = [
        'current_location' => 'array',  // Ensure this is cast to an array
        'destination' => 'array',       // Same for destination
    ];

    // Define the relationship with the Rider (User)
    public function rider()
    {
        return $this->belongsTo(User::class, 'rider_id');
    }

    // Define the relationship with the Selected Driver (User)
    public function selectedDriver()
    {
        return $this->belongsTo(TaxiDriver::class, 'selected_driver_id');
    }

    // Accessor for the status field (optional)
    public function getStatusAttribute($value)
    {
        return ucfirst($value);  // Capitalize the first letter of the status for better readability
    }

}
