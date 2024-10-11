<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Exceptions\CrudException;
use App\Http\Requests\CityRequest;
use App\Contracts\LocationInterface;
use App\Http\Resources\CityResource;
use App\Services\CityService;
use Exception;
use Illuminate\Container\Attributes\Log;
use Illuminate\Support\Facades\Config;

class CityController extends Controller
{
   private $locationInterface;
   private $cityService;

   public function __construct(LocationInterface $locationInterface, CityService $cityService ) {
    $this->locationInterface = $locationInterface;
    $this->cityService = $cityService;
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
        $validateData = $request->validated();
        try {
            $city = $this->locationInterface->store('City',$validateData);
        return new CityResource($city);
        } catch (\Exception $e) {
            throw CrudException::argumentCountError();
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
        ],Config::get('variable.NO_CONTENT'));
    }

    public function getCitiesByState($state_id)
    {
            $cities = $this->cityService->getCitiesByState($state_id);
            return response()->json($cities);
    }
}
