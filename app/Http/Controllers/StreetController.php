<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\CrudException;
use App\Contracts\LocationInterface;
use App\Http\Requests\StreetRequest;
use App\Http\Resources\StreetResource;
use App\Services\StreetService;
use Illuminate\Support\Facades\Config;

class StreetController extends Controller
{
    private $streetInterface;
    private $streetService;

    public function __construct(LocationInterface $streetInterface , StreetService $streetService) {
        $this->streetInterface = $streetInterface;
        $this->streetService = $streetService;
    }

    public function index()
    {
        $street = $this->streetInterface->all('Street');
        return StreetResource::collection($street);
    }

    public function store(StreetRequest $request)
    {
        $validateData = $request->validated();
        try {
            $street =$this->streetInterface->store('Street',$validateData);
            return new StreetResource($street);
        } catch (\Throwable $th) {
            throw CrudException::argumentCountError();
        }
    }
    public function update(StreetRequest $request, string $id)
    {
        $validateData = $request->validated();
        $street = $this->streetInterface->findById('Street',$id);
        if(!$street){
            return response()->json([
                'message'=>Config::get('variable.STREET_NOT_FOUND')
            ],Config::get('variable.SEVER_ERROR'));
        }
        $updateStreet = $this->streetInterface->update('Street',$validateData,$id);
        return new StreetResource($updateStreet);
    }

    public function destroy(string $id)
    {
        $street =$this->streetInterface->findById('Street',$id);

        if(!$street){
            return response()->json([
                'message'=>Config::get('variable.STREET_NOT_FOUND')
            ],Config::get('variable.SEVER_ERROR'));
        }

        $this->streetInterface->delete('Street',$id);
        return response()->json([
            'message'=>Config::get('variable.STREET_DELETED_SUCCESSFULLY')
        ],Config::get('variable.NO_CONTENT'));
    }

    public function getStreetsByWard($ward_id)
    {
        $streets = $this->streetService->getStreetsByWard($ward_id);
        return response()->json($streets);
    }
}
