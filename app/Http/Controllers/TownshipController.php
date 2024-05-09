<?php

namespace App\Http\Controllers;

use App\Models\Township;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Contracts\LocationInterface;
use App\Http\Requests\TownshipRequest;
use App\Http\Resources\TownshipResource;
use Illuminate\Support\Facades\Config;

class TownshipController extends Controller
{
    private $locationInterface;
    public function __construct(LocationInterface $locationInterface) {
        $this->locationInterface = $locationInterface;
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
       try {
        $validateData = $request->validated();
        $township = $this->locationInterface->store('Township',$validateData);
        if(!$township){
            return response()->json([
                'message'=>Config::get('variable.TNF')
            ],Config::get('variable.CLIENT_ERROR'));
        }
        return new TownshipResource($township);
       } catch (\Exception $e) {
        return ResponseHelper::jsonResponseWithClientError($e);
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
        ]);
    }

}
