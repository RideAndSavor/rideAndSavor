<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Http\Resources\FavoriteCuisineResource;

class FavoriteCuisine extends Controller
{
    public function __invoke()
    {
        $favoriteCuisinesWithImages = Food::favoriteCuisines()
            ->get();

        return FavoriteCuisineResource::collection($favoriteCuisinesWithImages);
    }
}
