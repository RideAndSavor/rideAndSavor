<?php

namespace App\Http\Controllers;

use App\Contracts\CountryInterface;
use App\Http\Requests\CountryRequest;
use App\Http\Resources\CountryResource;
use Illuminate\Http\Request;

class CountryController extends Controller
{
   private $countryInterface;

   public function __construct(CountryInterface $countryInterface ) {
    $this->countryInterface = $countryInterface;
   }
    public function index()
    {
        $country = $this->countryInterface->all();
        return CountryResource::collection($country);

    }

    public function create()
    {
        //
    }

    public function store(CountryRequest $request)
    {
        $validateData = $request->validated();
        // dd($validateData);
        $country = $this->countryInterface->store('Country',$validateData);
        // dd($country);
        if(!$country){
            return response()->json([
                'message'=>'Country Not Found'
            ],401);
        }
        return new CountryResource($country);
    }

    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
