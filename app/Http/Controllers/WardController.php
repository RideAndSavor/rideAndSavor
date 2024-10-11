<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\CrudException;
use App\Http\Requests\WardRequest;
use App\Contracts\LocationInterface;
use App\Http\Resources\WardResource;
use App\Services\WardService;
use Illuminate\Support\Facades\Config;

class WardController extends Controller
{

    private $wardInterface;
    private $wardService;
    public function __construct(LocationInterface $wardInterface , WardService $wardService) {
        $this->wardInterface = $wardInterface;
        $this->wardService = $wardService;
    }
    public function index()
    {

        $ward= $this->wardInterface->all('Ward');
        return WardResource::collection($ward);

    }

    public function store(WardRequest $request)
    {
       $validateData = $request->validated();
      try {
         $ward =$this->wardInterface->store('Ward',$validateData);
          return new WardResource($ward);
      } catch (\Throwable $th) {
        throw CrudException::argumentCountError();
      }
    }


    public function update(WardRequest $request, string $id)
    {
        $validateData = $request->validated();
        $ward = $this->wardInterface->findById('Ward',$id);
        if(!$ward){
            return response()->json([
                'message'=>Config::get('variable.WARD_NOT_FOUND')
            ],Config::get('variable.CLIENT_ERROR'));
        }
        $updateWard = $this->wardInterface->update('Ward',$validateData,$id);
        return new WardResource($updateWard);
    }

    public function destroy(string $id)
    {
        $ward = $this->wardInterface->findById('Ward', $id);

        if (!$ward) {
            return response()->json([
                'message' => Config::get('variable.WARD_NOT_FOUND')
            ], Config::get('variable.SEVER_ERROR'));
        }

       $this->wardInterface->delete('Ward', $id);

        return response()->json([
            'message'=>Config::get('variable.WARD_DELETED_SUCCESSFULLY')
        ], Config::get('variable.NO_CONTENT'));
    }

    public function getWardsByTownship($township_id)
    {
        $wards = $this->wardService->getWardsByTownship($township_id);
        return response()->json($wards);
    }

}
