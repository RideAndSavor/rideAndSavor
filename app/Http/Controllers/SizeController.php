<?php

namespace App\Http\Controllers;

use App\Models\Size;
use ArgumentCountError;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Exceptions\CrudException;
use App\Http\Requests\SizeRequest;
use App\Contracts\LocationInterface;
use App\Http\Resources\SizeResource;
use Illuminate\Support\Facades\Config;

class SizeController extends Controller
{
    private $sizeInterface;

    public function __construct(LocationInterface $sizeInterface)
    {
        $this->sizeInterface = $sizeInterface;
    }
    
    public function index()
    {
        try {
            $sizes = $this->sizeInterface->all('Size');

            return SizeResource::collection($sizes);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseWithConfigError($e);
        }
    }

    public function store(SizeRequest $request)
    {
        $validatedData = $request->validated();
        try {
            $size = $this->sizeInterface->store('Size', $validatedData);
            return new SizeResource($size);
        } catch (ArgumentCountError $e) {
            throw CrudException::argumentCountError();
        }
    }

    public function update(SizeRequest $request, string $id)
    {
        $validateData = $request->validated();
        try {
            $this->sizeInterface->findById('Size', $id);
            $updateSize = $this->sizeInterface->update('Size', $validateData, $id);
            return new SizeResource($updateSize);
        } catch (\Exception $e) {
            throw CrudException::prepareDataFormat();
        }
    }

    public function destroy(String $id)
    {
        $size = $this->sizeInterface->findById('Size', $id);
        if (!$size) {
            return response()->json([
                'message' => Config::get('variable.SZNF')
            ], Config::get('variable.SEVER_ERROR'));
        }
        $this->sizeInterface->delete('Size', $id);
        return response()->json([
            'message' => Config::get('variable.SZDS')
        ], Config::get('variable.OK'));
    }
}
