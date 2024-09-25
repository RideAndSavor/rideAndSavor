<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

trait NearByScope
{
    /**
     * Scope a query to get nearby drivers within a given radius.
     *
     * @param  Builder  $query
     * @param  float  $latitude
     * @param  float  $longitude
     * @param  int  $radius  Default radius in kilometers (default 1 km)
     * @return Builder
     */
    public function scopeNearby(Builder $query, $latitude, $longitude, $radius = 1)
    {
        return $query->selectRaw("*, (6371 * acos(cos(radians(?)) 
                * cos(radians(JSON_UNQUOTE(JSON_EXTRACT(current_location, '$.lat')))) 
                * cos(radians(JSON_UNQUOTE(JSON_EXTRACT(current_location, '$.long'))) 
                - radians(?)) 
                + sin(radians(?)) 
                * sin(radians(JSON_UNQUOTE(JSON_EXTRACT(current_location, '$.lat')))))) AS distance", 
                [$latitude, $longitude, $latitude])
            ->where('is_available', 1)
            ->having('distance', '<=', $radius)
            ->orderBy('distance', 'asc');
    }
}
