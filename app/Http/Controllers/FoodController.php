<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Exceptions\CrudException;
use App\Http\Requests\FoodRequest;
use App\Contracts\LocationInterface;
use App\Http\Resources\FoodResource;
use Illuminate\Support\Facades\Config;

class FoodController extends Controller
{
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
        try {
            $food = $this->foodInterface->store('Food',$validateData);
            return new FoodResource($food);
        } catch (\Throwable $th) {
            throw CrudException::argumentCountError();
        }
    }

    public function update(FoodRequest $request, string $id)
    {
        $validateData = $request->validated();
        try {
            $this->foodInterface->findById('Food',$id);
            $updateFood = $this->foodInterface->update('Food',$validateData,$id);
            return new FoodResource($updateFood);
        } catch (\Throwable $th) {
            throw CrudException::argumentCountError();
        }
    }

    public function destroy(String $id)
    {
        $food = $this->foodInterface->findById('Food', $id);
        if (!$food) {
            return response()->json([
                'message' => Config::get('variable.FOOD_NOT_FOUND')
            ], Config::get('variable.SEVER_ERROR'));
        }
        $country = $this->foodInterface->delete('Food', $id);
        return response()->json([
            'message' => Config::get('variable.FOOD_DELETED_SUCCESSFULLY')
        ], Config::get('variable.OK'));
    }
}
