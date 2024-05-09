<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\LocationInterface;
use App\Helpers\ResponseHelper;
use App\Http\Requests\CityRequest;
use App\Http\Resources\CityResource;
use Illuminate\Support\Facades\Config;

class CityController extends Controller
{
   private $locationInterface;

   public function __construct(LocationInterface $locationInterface ) {
    $this->locationInterface = $locationInterface;
   }
    public function index()
    {
        try {
            $city = $this->locationInterface->all('City');
            return CityResource::collection($city);
        } catch (\Exception $e) {
           return ResponseHelper::jsonResponseWithConfigError($e);
        }
    }

    public function store(CityRequest $request)
    {
        try {
            $validateData = $request->validated();
            $city = $this->locationInterface->store('City',$validateData);
            if(!$city){
                return response()->json([
                    'message'=>Config::get('variable.CITY_NOT_FOUND')
                ],Config::get('variable.CLIENT_ERROR'));
            }
        return new CityResource($city);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseWithClientError($e);
        }
    }

    public function update(CityRequest $request, string $id)
    {
       try {
        $validateData = $request->validated();
        $city = $this->locationInterface->findById('City',$id);
        if(!$city){
            return response()->json([
                'message'=>Config::get('variable.CITY_NOT_FOUND')
            ],Config::get('variable.CLIENT_ERROR'));
        }
        $city = $this->locationInterface->update('City',$validateData,$id);
        return new CityResource($city);
       } catch (\Exception $e) {
        return ResponseHelper::jsonResponseWithConfigError($e);
       }
    }

    public function destroy(string $id)
    {
        $country = $this->locationInterface->findById('City',$id);
        if(!$country){
            return response()->json([
                'message'=>Config::get('variable.CITY_NOT_FOUND')
            ],Config::get('variable.SEVER_ERROR'));
        }
        $country = $this->locationInterface->delete('City',$id);
        return response()->json([
            'message'=>Config::get('variable.CITY_DELETED_SUCCESSFULLY')
        ]);
    }
}
