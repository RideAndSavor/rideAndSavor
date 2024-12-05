<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Food;
use App\Models\User;
use App\Models\Ward;
use App\Models\State;
use App\Models\Street;
use App\Models\Address;
use App\Models\Country;
use App\Models\Topping;
use App\Models\Category;
use App\Models\Township;
use App\Models\Restaurant;
use App\Models\SubCategory;
use App\Traits\SearchTrait;
use App\Models\DiscountItem;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    use SearchTrait;

    private $previousResults = [];

    public function search(Request $request)
    {
        $query = $request->input('q');
        $type = $request->input('type', 'main'); // Default to 'main'

        if (!$query) {
            return response()->json(['error' => 'Query parameter "q" is required.'], 400);
        }

        $terms = array_map('trim', explode(',', $query)); // Split and trim terms

        // Filter terms based on previous results
        if (!empty($this->previousResults)) {
            $terms = $this->filterSearchTerms($terms, $this->previousResults);
        }

        // Get the list of models to search based on type
        $models = $this->getSearchModels($type);

        if (empty($models)) {
            return response()->json(['error' => 'No results found.'], 400);
        }

        // Perform the search using the trait's method
        $response = $this->searchModels($models, $terms);

        // Store the results for potential future filtering
        $this->previousResults = $response;

        if (empty($response)) {
            return response()->json(['message' => 'No results found.'], 404);
        }

        return response()->json($response);
    }

    private function getSearchModels($type)
    {
        $foodModels = [
            'countries' => [Country::class, ['name']],
            'states' => [State::class, ['name']],
            'cities' => [City::class, ['name']],
            'townships' => [Township::class, ['name']],
            'wards' => [Ward::class, ['name']],
            'streets' => [Street::class, ['name']],
            'categories' => [Category::class, ['name']],
            'sub_categories' => [SubCategory::class, ['name']],
            'foods' => [Food::class, ['name']],
            'discount_items' => [DiscountItem::class, ['name']],
            'restaurants' => [Restaurant::class, ['name']],
        ];

        $mallModels = [
            
        ];

        switch ($type) {
            case 'food':
                return $foodModels;

            case 'mall':
                return $mallModels;

            case 'main':
                return array_merge($foodModels, $mallModels);

            default:
                return [];
        }
    }

    private function filterSearchTerms($terms, $previousResults)
    {
        $filteredTerms = [];

        foreach ($terms as $term) {
            foreach ($previousResults as $results) {
                foreach ($results as $data) {
                    if (stripos($data->name ?? '', $term) !== false) {
                        $filteredTerms[] = $term;
                        break;
                    }
                }
            }
        }

        return $filteredTerms;
    }
}