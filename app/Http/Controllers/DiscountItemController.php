<?php

namespace App\Http\Controllers;

use App\Helpers\ResponseHelper;
use App\Exceptions\CrudException;
use App\Contracts\LocationInterface;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\DiscountItemRequest;
use App\Http\Resources\DiscountItemResource;

class DiscountItemController extends Controller
{
    private $discountItemInterface;

    public function __construct(LocationInterface $locationInterface)
    {
        $this->discountItemInterface = $locationInterface;
    }

    public function index()
    {
        try {
            $discountItem = $this->discountItemInterface->relationData('DiscountItem', 'percentage');
            return DiscountItemResource::collection($discountItem);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseWithConfigError($e);
        }
    }


    public function store(DiscountItemRequest $discountItemRequest)
    {
        $validatedData = $discountItemRequest->validated();
        try {
            $percentage = $this->discountItemInterface->store('DiscountItem', $validatedData);
            return new DiscountItemResource($percentage);
        } catch (\Throwable $th) {
            CrudException::argumentCountError();
        }
    }


    public function update(DiscountItemRequest $discountItemRequest, string $id)
    {
        $validateData = $discountItemRequest->validated();
        $discountItem = $this->discountItemInterface->findById('DiscountItem', $id);
        if (!$discountItem) {
            return response()->json([
                'message' => Config::get('variable.DINF')
            ], Config::get('variable.CLIENT_ERROR'));
        }
        try {
            $updatePercentage = $this->discountItemInterface->update('DiscountItem', $validateData, $id);
            return new DiscountItemResource($updatePercentage);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseWithConfigError($e);
        }
    }

    public function destroy(string $id)
    {
        $discountItem = $this->discountItemInterface->findById('DiscountItem', $id);
        if (!$discountItem) {
            return response()->json([
                'message' => Config::get('variable.DINF')
            ], Config::get('variable.CLIENT_ERROR'));
        }
        $this->discountItemInterface->delete('DiscountItem', $id);
        return response()->json([
            'message' => Config::get('variable.DIDSF')
        ], Config::get('variable.NO_CONTENT'));
    }
}
