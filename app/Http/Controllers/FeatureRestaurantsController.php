<?php

namespace App\Http\Controllers;
use App\Models\Restaurant;
use App\Traits\CanLoadRelationships;
use App\Http\Resources\RestaurantResource;


class FeatureRestaurantsController extends Controller
{
    use CanLoadRelationships;
    private array $relations = [
        'ratings',
        'comments',
        'restaurantImages'
    ];
    public function __invoke()
    {
        $featureRestaurants = $this->loadRelationships(Restaurant::featureRestaurants())->get();
        return RestaurantResource::collection($featureRestaurants);
    }
}
