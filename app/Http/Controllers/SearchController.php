<?php

namespace App\Http\Controllers;

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
use App\Models\Ward;
use App\Models\Street;
use App\Models\Address;

class SearchController extends Controller
{
    private $previousResults = [];

    public function search(Request $request)
    {
        $query = $request->input('q');

        if (!$query) {
            return response()->json(['error' => 'Query parameter "q" is required.'], 400);
        }

        $terms = explode(',', $query);
        $terms = array_map('trim', $terms); // Trim whitespace around each term

        $response = [];

        // If previous results are available, filter search terms to those results
        if (!empty($this->previousResults)) {
            $terms = $this->filterSearchTerms($terms, $this->previousResults);
        }

        // Define the models and their searchable fields
        $models = [
            'users' => [User::class, ['name', 'email']],
            'categories' => [Category::class, ['name']],
            'sub_categories' => [SubCategory::class, ['name']],
            'foods' => [Food::class, ['name']], 
            'discount_items' => [DiscountItem::class, ['name']],
            'ingredients' => [Ingredient::class, ['name']],
            'restaurants' => [Restaurant::class, ['name']],
            'countries' => [Country::class, ['name']],
            'states' => [State::class, ['name']],
            'cities' => [City::class, ['name']],
            'townships' => [Township::class, ['name']],
            'wards' => [Ward::class, ['name']],
            'streets' => [Street::class, ['name']],
            'addresses' => [Address::class, ['block_no', 'floor']],
        ];

        foreach ($models as $key => $modelConfig) {
            $model = $modelConfig[0];
            $searchableFields = $modelConfig[1];

            $results = $model::where(function($q) use ($terms, $searchableFields) {
                foreach ($terms as $term) {
                    foreach ($searchableFields as $field) {
                        $q->orWhere($field, 'like', '%' . $term . '%');
                    }
                }
            })->get();

            if ($results->isNotEmpty()) {
                $response[$key] = $results;
            }
        }

        // Save current results for future filtering
        $this->previousResults = $response;

        if (empty($response)) {
            return response()->json(['message' => 'No results found.'], 404);
        }

        return response()->json($response);
    }

    // Filter search terms to previous search results
    private function filterSearchTerms($terms, $previousResults)
    {
        $filteredTerms = [];

        foreach ($terms as $term) {
            foreach ($previousResults as $result) {
                foreach ($result as $data) {
                    if (strpos(strtolower($data->name), strtolower($term)) !== false) {
                        $filteredTerms[] = $term;
                        break;
                    }
                }
            }
        }

        return $filteredTerms;
    }
}
