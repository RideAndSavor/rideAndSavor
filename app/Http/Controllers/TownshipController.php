<?php

namespace App\Http\Controllers;

use App\Models\Township;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Exceptions\CrudException;
use App\Contracts\LocationInterface;
use App\Http\Requests\TownshipRequest;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\TownshipResource;
use App\Services\TownshipService;

class TownshipController extends Controller
{
    private $locationInterface;
    private $townshipService;
    public function __construct(LocationInterface $locationInterface, TownshipService $townshipService) {
        $this->locationInterface = $locationInterface;
        $this->townshipService = $townshipService;
    }

    public function index()
    {
       try {
        $township= $this->locationInterface->all('Township');
        return TownshipResource::collection($township);
       } catch (\Exception $e) {
        return ResponseHelper::jsonResponseWithConfigError($e);
       }
    }
    public function store(TownshipRequest $request)
    {
        $validateData = $request->validated();
       try {
        $township = $this->locationInterface->store('Township',$validateData);
        return new TownshipResource($township);
       } catch (\Exception $e) {
        throw CrudException::argumentCountError();

       }
    }
    public function update(TownshipRequest $request, string $id)
    {
       try {
        $validateData = $request->validated();
        $township =$this->locationInterface->findById('Township',$id);
        if(!$township){
            return response()->json([
                'message'=>Config::get('variable.TNF')
            ],Config::get('variable.CLIENT_ERROR'));
        }
        $township =$this->locationInterface->update('Township',$validateData,$id);
        return new TownshipResource($township);
       } catch (\Exception $e) {
        return ResponseHelper::jsonResponseWithConfigError($e);
       }
    }

    public function destroy(string $id)
    {
        $country = $this->locationInterface->findById('Township',$id);
        if(!$country){
            return response()->json([
                'message'=>Config::get('variable.TNF')
            ],Config::get('variable.SEVER_ERROR'));
        }
        $country = $this->locationInterface->delete('Township',$id);
        return response()->json([
            'message'=>Config::get('variable.TDS')
        ],Config::get('variable.NO_CONTENT'));
    }

    public function getTownshipsByCity($city_id)
    {
        $townships = $this->townshipService->getTownshipsByCity($city_id);
        return response()->json($townships);
    }

}
