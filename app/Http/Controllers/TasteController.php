<?php

namespace App\Http\Controllers;

use Exception;
use ArgumentCountError;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Exceptions\CrudException;
use App\Http\Requests\TasteRequest;
use App\Contracts\LocationInterface;
use App\Http\Resources\TasteResource;
use Illuminate\Support\Facades\Config;

class TasteController extends Controller
{
    private $tasteInterface;

    public function __construct(LocationInterface $tasteInterface)
    {
        $this->tasteInterface = $tasteInterface;
    }

    public function index()
    {
        try
        {
            $tastes = $this->tasteInterface->all('Taste');
            return TasteResource::collection($tastes);
        }
        catch (\Exception $e) {
            return ResponseHelper::jsonResponseWithConfigError($e);
        }
    }

    public function store(TasteRequest $request)
    {
        $validatedData = $request->validated();
        try
        {
            $taste = $this->tasteInterface->store('Taste', $validatedData);
            return new TasteResource($taste);
        }
        catch (ArgumentCountError $e)
        {
            throw CrudException::argumentCountError();
        }
    }

    public function update(TasteRequest $request, string $id)
    {
        $validatedData = $request->validated();
        try
        {
            $this->tasteInterface->findById('Taste', $id);
            $updateTaste = $this->tasteInterface->update('Taste', $validatedData, $id);
            return new TasteResource($updateTaste);
        }
        catch (\Exception $e)
        {
            throw CrudException::prepareDataFormat();
        }
    }

    public function destroy (String $id)
    {
        $taste = $this->tasteInterface->findById('Taste', $id);
        if(!$taste)
        {
            return response()->json(['message' => Config::get('variable.TASTENF')],
             Config::get('variable.SEVER_ERROR'));
        }
        $this->tasteInterface->delete('Taste', $id);
        return response()->json(['message' => Config::get('variable.TASTEDS')], 
         Config::get('variable.OK'));
    }

}
