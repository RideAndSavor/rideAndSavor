<?php

namespace App\Http\Controllers;

use App\Contracts\LocationInterface;
use App\Http\Requests\WardRequest;
use App\Http\Resources\WardResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class WardController extends Controller
{

    private $wardInterface;
    public function __construct(LocationInterface $wardInterface) {
        $this->wardInterface = $wardInterface;
    }
    public function index()
    {

        $ward= $this->wardInterface->all('Ward');
        return WardResource::collection($ward);

    }

    public function store(WardRequest $request)
    {
       $validateData = $request->validated();
       $ward =$this->wardInterface->store('Ward',$validateData);
       if(!$ward){
        return response()->json([
            'message'=>Config::get('variable.WARD_NOT_FOUND')
        ],Config::get('variable.CLIENT_ERROR'));
       }

       return new WardResource($ward);
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

}
