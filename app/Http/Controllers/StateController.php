<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StateService;
use App\Helpers\ResponseHelper;
use App\Exceptions\CrudException;
use App\Http\Requests\StateRequest;
use App\Contracts\LocationInterface;
use App\Http\Resources\StateResource;
use Illuminate\Support\Facades\Config;

class StateController extends Controller
{
   private $locationInterface;
   private $stateService;

   public function __construct(LocationInterface $locationInterface, StateService $stateService) {
      $this->locationInterface = $locationInterface;
      $this->stateService = $stateService;
   }
   public function index()
   {
       try {
           $states = $this->locationInterface->all('State');
           return StateResource::collection($states);
       } catch (\Exception $e) {
           return ResponseHelper::jsonResponseWithConfigError($e);
       }
   }

   public function store(StateRequest $request)
    {
        $validatedData = $request->validated();
        try {
            $state = $this->locationInterface->store('State', $validatedData);
            return new StateResource($state);
        } catch (\Exception $e) {
            throw CrudException::argumentCountError();
        }
    }

    public function update(StateRequest $request, string $id)
    {
        try {
            $validatedData = $request->validated();
            $state = $this->locationInterface->findById('State', $id);
            if (!$state) {
                return response()->json([
                    'message' => Config::get('variable.SNF')
                ], Config::get('variable.CLIENT_ERROR'));
            }
            $updatedState = $this->locationInterface->update('State', $validatedData, $id);
            return new StateResource($updatedState);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseWithConfigError($e);
        }
    }

    public function destroy(string $id)
    {
        $country = $this->locationInterface->findById('State',$id);
        if(!$country){
            return response()->json([
                'message'=>Config::get('variable.SNF')
            ],Config::get('variable.SEVER_ERROR'));
        }
        $country = $this->locationInterface->delete('State',$id);
        return response()->json([
            'message'=>Config::get('variable.SDF')
        ],Config::get('variable.NO_CONTENT'));
    }

    public function getStatesByCountry($country_id)
    {
        $states = $this->stateService->getStatesByCountry($country_id);
        return response()->json($states);
    }

}
