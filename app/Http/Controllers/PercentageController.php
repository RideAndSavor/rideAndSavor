<?php

namespace App\Http\Controllers;

use App\Http\Resources\PercentageResource;
use App\Contracts\LocationInterface;
use App\Exceptions\CrudException;
use App\Http\Requests\PercentageRequest;
use Illuminate\Support\Facades\Config;

class PercentageController extends Controller
{

    private $percentageInterface;

    public function __construct(LocationInterface $percentageInterface)
    {
        $this->percentageInterface = $percentageInterface;
    }

    public function index()
    {
        try {
            $percentage = $this->percentageInterface->all('Percentage');
            return PercentageResource::collection($percentage);
        } catch (\Throwable $th) {
            CrudException::emptyData();
        }
    }

    public function store(PercentageRequest $request)
    {
        $validateData = $request->validated();
        try {
            $percentage = $this->percentageInterface->store('Percentage', $validateData);
            return new PercentageResource($percentage);
        } catch (\Throwable $th) {
            CrudException::argumentCountError();
        }
    }

    public function update(PercentageRequest $request, string $id)
    {
        $validateData = $request->validated();
        try {
            $this->percentageInterface->findById('Percentage', $id);
            $updatePercentage = $this->percentageInterface->update('Percentage', $validateData, $id);
            return new PercentageResource($updatePercentage);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => Config::get('variable.PERCENTAGE_NOT_FOUND')
            ], Config::get('variable.SEVER_ERROR'));
        }
    }


    public function destroy(string $id)
    {
        $percentage = $this->percentageInterface->findById('Percentage', $id);
        if (!$percentage) {
            return response()->json([
                'message' => Config::get('variable.FAILE_TO_DELETED_PERCENTAGE')
            ], Config::get('variable.SEVER_ERROR'));
        }

        $this->percentageInterface->delete('Percentage', $id);
        return response()->json([
            'message'=>Config::get('variable.PERCENTAGE_DELETED_SUCCESSFULLY')
        ],Config::get('variable.OK'));
    }
}
