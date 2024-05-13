<?php

namespace App\Http\Controllers;

use App\Http\Resources\IngredientResource;
use Illuminate\Http\Request;
use App\Contracts\LocationInterface;
use App\Http\Requests\IngredientRequest;
use Illuminate\Support\Facades\Config;

class IngredientController extends Controller
{
    private $ingredientInterface;

    public function __construct(LocationInterface $ingredientInterface) {
        $this->ingredientInterface = $ingredientInterface;
    }

    public function index()
    {
        $ingredient =$this->ingredientInterface->all('Ingredient');
        return IngredientResource::collection($ingredient);

    }

    public function store(IngredientRequest $request)
    {
        $validateData = $request->validated();
        $ingredient = $this->ingredientInterface->store('Ingredient',$validateData);
        if(!$ingredient){
            return response()->json([
                'message'=>Config::get('variable.INGREDIENTS_NOT_FOUND')
            ],Config::get('variable.CLIENT_ERROR'));
        }
        return new IngredientResource($ingredient);
    }


    public function update(IngredientRequest $request, string $id)
    {
        $validateData = $request->validated();

        $ingredient = $this->ingredientInterface->findById('Ingredient',$id);
        if(!$ingredient){
            return response()->json([
                'message'=>Config::get('variable.INGREDIENTS_NOT_FOUND')
            ],Config::get('variable.SEVER_ERROR'));
        }
        $updateIngredient = $this->ingredientInterface->update('Ingredient',$validateData,$id);
        return new IngredientResource($updateIngredient);
    }

    public function destroy(string $id)
    {
        $ingredient = $this->ingredientInterface->findById('Ingredient',$id);
        if(!$ingredient){
            return response()->json([
                'message'=>Config::get('variable.FAIL_TO_DELETED_INGREDIENT')
            ],Config::get('variable.SEVER_ERROR'));
        }

        $this->ingredientInterface->delete('Ingredient',$id);
        return response()->json([
            'message'=>Config::get('variable.INGREDIENT_DELETED_SUCCESSFULLY')
        ]);
    }
}
