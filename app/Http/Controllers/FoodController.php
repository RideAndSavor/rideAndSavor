<?php

namespace App\Http\Controllers;

use App\Models\Food;
use App\Models\Images;
use App\Traits\ImageTrait;
use App\Traits\AddressTrait;
use App\Helpers\ResponseHelper;
use App\Exceptions\CrudException;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\FoodRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Contracts\LocationInterface;
use App\Http\Resources\FoodResource;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class FoodController extends Controller
{
    use AddressTrait,ImageTrait;
    private $foodInterface;
    private $genre;

    public function __construct(LocationInterface $foodInterface) {
        $this->foodInterface = $foodInterface;
        $this->genre = Config::get('variable.FOOD_IMAGE');
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
    $folder_name = 'public/foods/';
    $tableName= 'images';
    $validateData = $request->validated();

    $ingredientIds = $validateData['ingredient_id'] ?? [];
    unset($validateData['upload_url']);
    unset($validateData['ingredient_id']);

    try {
        $food = $this->foodInterface->store('Food', $validateData);

        if($request->hasFile('upload_url')){
            $this->storeImage($request,$food->id,$this->genre,$this->foodInterface,$folder_name,$tableName);
        }

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
        $folder_name = 'public/foods';
        $tableName = 'images';
        $validateData = $request->validated();

        $food = $this->updateFoodIngredient($validateData,$id);
        if($food instanceof JsonResponse){
            return $food;
        }

        if($request->hasFile('upload_url')){
            $this->updateImage($request,$food->id,$this->genre,$this->foodInterface,$folder_name,$tableName,$id);
        }

        if ($request->hasFile('upload_url')) {
            try {
                $this->updateImage($request, $food->id, $this->genre, $this->foodInterface, $folder_name, $tableName, $id);
            } catch (\Exception $e) {
                Log::error('Error updating image in FoodController@update: ' . $e->getMessage());
                return response()->json([
                    'message' => 'Image update failed.'
                ], 500);
            }
        }

        return new FoodResource($food);
    }

    public function destroy(String $id)
    {
        $food = $this->deletedFoodIngredient($id);
        if($food instanceof JsonResponse){
        return $food;
         }

         $imageId = $food->image_id;
         $this->deleteImage(Images::class,$imageId,'upload_url');
        return response()->json([
            'message' => Config::get('variable.FOOD_DELETED_SUCCESSFULLY')
        ], Config::get('variable.OK'));
    }

    public function getPopularFoods()
    {
        dd(Food::withCount('orderDetails as orders_count'));
        $popularFoods = Food::withCount('orderDetails as orders_count')
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get();

        return response()->json($popularFoods);
    }
}
