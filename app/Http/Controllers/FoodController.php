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
use Illuminate\Support\Facades\Log;
use App\Contracts\LocationInterface;
use App\Http\Resources\FoodResource;
use App\Traits\CanLoadRelationships;
use Illuminate\Support\Facades\Config;

class FoodController extends Controller
{
    use AddressTrait, ImageTrait, CanLoadRelationships;
    private array $relations = [
        'toppings',
        'foodImages'
    ];
    private $foodInterface,$genre;

    public function __construct(LocationInterface $foodInterface)
    {
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
        $tableName = 'images';
        $validateData = $request->validated();

        $toppingIds = $validateData['topping_id'] ?? [];
        unset($validateData['upload_url']);
        unset($validateData['topping_id']);

        try {
            $food = $this->foodInterface->store('Food', $validateData);

            if ($request->hasFile('upload_url')) {
                $this->storeImage($request, $food->id, $this->genre, $this->foodInterface, $folder_name, $tableName);
            }

            if (!empty($toppingIds)) {
                $food->toppings()->attach($toppingIds);
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

        $food = $this->updateFoodTopping($validateData, $id);
        if ($food instanceof JsonResponse) {
            return $food;
        }

        if ($request->hasFile('upload_url')) {
            $this->updateImage($request, $food->id, $this->genre, $this->foodInterface, $folder_name, $tableName, $id);
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

    public function show(Food $food): FoodResource
    {

        return new FoodResource($this->loadRelationships(Food::where('id', $food->id))->first());
    }

    public function destroy(string $id)
    {
        $food = $this->deletedFoodTopping($id);
        if ($food instanceof JsonResponse) {
            return $food;
        }

        $imageId = $food->image_id;
        $this->deleteImage(Images::class, $imageId, 'upload_url');
        return response()->json([
            'message' => Config::get('variable.FOOD_DELETED_SUCCESSFULLY')
        ], Config::get('variable.OK'));
    }

    public function getPopularFoods()
    {
        $popularFoods = Food::withCount('orderDetails as orders_count')
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get();

        return response()->json($popularFoods);
    }
}
