<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Food;
use App\Models\DiscountItem;
use App\Models\Ingredient;
use App\Models\Restaurant;
use App\Models\Country;
use App\Models\State;
use App\Models\City;
use App\Models\Township;
use App\Models\Street;
use App\Models\Ward;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json(['error' => 'Query parameter "q" is required.'], 400);
        }

        $models = [
            'users' => User::class,
            'categories' => Category::class,
            'sub_categories' => SubCategory::class,
            'foods' => Food::class,
            'discount_items' => DiscountItem::class,
            'ingredients' => Ingredient::class,
            'restaurants' => Restaurant::class,
            'countries' => Country::class,
            'states' => State::class,
            'cities' => City::class,
            'townships' => Township::class,
            'wards' => Ward::class,
            'streets' => Street::class,
            'addresses' => Address::class,
        ];

        $response = [];
        foreach ($models as $key => $model) {
            $results = $model::search($query)->get();
            if ($results->isNotEmpty()) {
                $response[$key] = $results;
            }
        }

        return response()->json($response);
    }
}
