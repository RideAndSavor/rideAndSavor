<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\LocationInterface;
use App\Http\Requests\StreetRequest;
use App\Http\Resources\StreetResource;
use Illuminate\Support\Facades\Config;

class StreetController extends Controller
{
    private $streetInterface;

    public function __construct(LocationInterface $streetInterface) {
        $this->streetInterface = $streetInterface;
    }

    public function index()
    {
        $street = $this->streetInterface->all('Street');
        return StreetResource::collection($street);
    }

    public function store(StreetRequest $request)
    {
        $validateData = $request->validated();
        $street =$this->streetInterface->store('Street',$validateData);
        if(!$street){
            return response()->json([
                'message'=>Config::get('variable.STREET_NOT_FOUND')
            ],Config::get('variable.CLIENT_ERROR'));
        }
        return new StreetResource($street);
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
}
