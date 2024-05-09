<?php

namespace App\Http\Controllers;

use App\Contracts\LocationInterface;
use App\Helpers\ResponseHelper;
use Illuminate\Http\Request;
use App\Http\Requests\StateRequest;
use App\Http\Resources\StateResource;
use Illuminate\Support\Facades\Config;

class StateController extends Controller
{
   private $locationInterface;

   public function __construct(LocationInterface $locationInterface) {
      $this->locationInterface = $locationInterface;
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
    try {
        $validatedData = $request->validated();
        $state = $this->locationInterface->store('State', $validatedData);
        if (!$state) {
            return response()->json([
                'message' => Config::get('variable.SNF'),
            ],Config::get('variable.CLIENT_ERROR'));
        }
        return new StateResource($state);
    } catch (\Exception $e) {
        return ResponseHelper::jsonResponseWithClientError($e);
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
    ]);
}
}
