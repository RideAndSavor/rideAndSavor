<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Contracts\LocationInterface;
use App\Http\Requests\CountryRequest;
use App\Http\Resources\CountryResource;

class CountryController extends Controller
{
   private $locationInterface;

   public function __construct(LocationInterface $locationInterface ) {
    $this->locationInterface = $locationInterface;
   }
    public function index()
    {
        $country = $this->locationInterface->all('Country');
        return CountryResource::collection($country);

    }

    public function store(CountryRequest $request)
    {
        $validateData = $request->validated();
        // dd($validateData);
        $country = $this->locationInterface->store('Country',$validateData);
        // dd($country);
        if(!$country){
            return response()->json([
                'message'=>'Country Not Found'
            ],401);
        }
        return new CountryResource($country);
    }

    public function edit(string $id)
    {
        //
    }

    public function update(CountryRequest $request, string $id)
    {
       $validateData = $request->validated();
       $country = $this->locationInterface->findById('Country',$id);
       if(!$country){
        return response()->json([
            'message'=>'Country Not Found'
        ],401);
       }
       $country = $this->locationInterface->update('Country',$validateData,$id);
       return new CountryResource($country);
    }

    public function destroy(string $id)
    {
        $country = $this->locationInterface->findById('Country',$id);
        if(!$country){
            return response()->json([
                'message'=>'Country Not Found'
            ],401);
        }
        $country = $this->locationInterface->delete('Country',$id);
        return response()->json([
            'message'=>"Country Deleted Successfully"
        ]);
    }
}
