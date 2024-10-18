<?php

namespace App\Http\Controllers;
use App\Models\Restaurant;
use App\Traits\CanLoadRelationships;
use App\Http\Resources\RestaurantResource;

class PopularRestaurants extends Controller
{
    use CanLoadRelationships;
    private array $relations = [
        'ratings',
        'comments',
        'restaurantImages'
    ];
    public function __invoke()
    {
        $popularRestaurants = $this->loadRelationships(Restaurant::PopularRestaurants())->get();
        return RestaurantResource::collection($popularRestaurants);
    }
}
