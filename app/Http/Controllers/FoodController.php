<?php

namespace App\Http\Controllers;

use App\Traits\AddressTrait;
use App\Helpers\ResponseHelper;
use App\Exceptions\CrudException;
use App\Http\Requests\FoodRequest;
use Illuminate\Support\Facades\Log;
use App\Contracts\LocationInterface;
use App\Http\Resources\FoodResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;

class FoodController extends Controller
{
    use AddressTrait;
    private $foodInterface;

    public function __construct(LocationInterface $foodInterface) {
        $this->foodInterface = $foodInterface;
    }

    public function index()
    {
        try {
            $food = $this->foodInterface->all('Food');
            return FoodResource::collection($food);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseWithConfigError($e);
        }
    }

    public function store(FoodRequest $request)
{
    $validateData = $request->validated();
    $ingredientIds = $validateData['ingredient_id'] ?? [];
    unset($validateData['ingredient_id']);

    try {
        $food = $this->foodInterface->store('Food', $validateData);
        if (!empty($ingredientIds)) {
            $food->ingredients()->attach($ingredientIds);
        }
        return new FoodResource($food);
    } catch (\Exception $e) {
        Log::error('Error in FoodController@store: ' . $e->getMessage());
        throw CrudException::argumentCountError();
    }
}

    public function update(FoodRequest $request, string $id)
    {
        $validateData = $request->validated();
        $food = $this->updateFoodIngredient($validateData,$id);
        if($food instanceof JsonResponse){
            return $food;
        }
        return new FoodResource($food);

    }

    public function destroy(String $id)
    {
        $food = $this->deletedFoodIngredient($id);
        if($food instanceof JsonResponse){
        return $food;
         }
        return response()->json([
            'message' => Config::get('variable.FOOD_DELETED_SUCCESSFULLY')
        ], Config::get('variable.OK'));
    }


}
