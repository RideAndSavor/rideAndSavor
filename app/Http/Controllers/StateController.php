<?php

namespace App\Http\Controllers;

use App\Contracts\LocationInterface;
use Illuminate\Http\Request;
use App\Http\Requests\StateRequest;
use App\Http\Resources\StateResource;

class StateController extends Controller
{
   private $locationInterface;

   public function __construct(LocationInterface $locationInterface) {
      $this->locationInterface = $locationInterface;
   }
    public function index()
    {
      $state = $this->locationInterface->all('State');
      return StateResource::collection($state);
    }

    public function store(StateRequest $request)
    {
        $validateData = $request->validated();
        // dd($validateData);
        $state =$this->locationInterface->store('State',$validateData);
        if(!$state){
            return response()->json([
                'message'=>'State Not Found',
            ],401);
        }
        return new StateResource($state);
    }

    public function update(StateRequest $request, string $id)
    {
        $validateData = $request->validated();
        // dd($validateData);
        $state =$this->locationInterface->findById('State',$id);
        if(!$state){
            return response()->json([
                'message'=>"State Not Found"
            ],401);
        }
        $state =$this->locationInterface->update('State',$validateData,$id);
        return new StateResource($state);
    }

    public function destroy(string $id)
    {
        $state =$this->locationInterface->findById('State',$id);
        if(!$state){
            return response()->json([
                'message'=>'State Not Found'
            ],401);
        }
        $state  = $this->locationInterface->delete('State',$id);
        return response()->json([
            'message'=>'State Deleted Successfully'
        ]);
    }
}
