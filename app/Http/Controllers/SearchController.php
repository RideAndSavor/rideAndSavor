<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Country;
use App\Models\Food;
use App\Models\State;
use App\Models\SubCategory;
use App\Models\Township;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json(['error' => 'Query parameter "q" is required.'], 400);
        }

        // Perform searches using Laravel Scout
        $userResults = User::search($query)->get();
        $categoryResults = Category::search($query)->get(); 
        $subcategoryResults = SubCategory::search($query)->get();       
        $countryResults = Country::search($query)->get();
        $stateResults = State::search($query)->get();
        $foodResults = Food::search($query)->get();
        $townshipResults = Township::search($query)->get();

        $response = [];
        if ($userResults->isNotEmpty()) {
            $response['users'] = $userResults;
        }

        if ($categoryResults->isNotEmpty()) {
            $response['categories'] = $categoryResults;
        }

        if ($foodResults->isNotEmpty()) {
            $response['foods'] = $foodResults;
        }

        if ($subcategoryResults->isNotEmpty()) {
            $response['sub_categories'] = $subcategoryResults;
        }

        if ($townshipResults->isNotEmpty()) {
            $response['townships'] = $townshipResults;
        }

        if ($countryResults->isNotEmpty()) {
            $response['countries'] = $countryResults;
        }

        if ($stateResults->isNotEmpty()) {
            $response['states'] = $stateResults;
        }

        return response()->json($response);
    }
}

