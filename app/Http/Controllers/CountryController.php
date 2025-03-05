<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Country;
use ArgumentCountError;
use App\Helpers\ResponseHelper;
use App\Exceptions\CrudException;
use App\Exceptions\CustomException;
use App\Contracts\LocationInterface;
use App\Http\Requests\CountryRequest;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\CountryResource;

class CountryController extends Controller
{
    private $locationInterface;

    public function __construct(LocationInterface $locationInterface)
    {
        $this->locationInterface = $locationInterface;
    }
    public function index()
    {
        // try {
            $countries = $this->locationInterface->all('Country');
            return response()->json(CountryResource::collection($countries)->toArray(request()), 200);
        // } catch (\Exception $e) {
            // return ResponseHelper::jsonResponseWithConfigError($e);
        // }
    }

    public function store(CountryRequest $request)
    {
        $validatedData = $request->validated();
        try {
            $country = $this->locationInterface->store('Country', $validatedData);
            // throw CustomException::created();
            return new CountryResource($country);
        } catch (ArgumentCountError $e) {
            throw CrudException::argumentCountError();
        }
    }

    public function update(CountryRequest $request, string $id)
    {
        try {
            $validateData = $request->validated();
            $country = $this->locationInterface->findById('Country', $id);
            if (!$country) {
                return response()->json([
                    'message' => Config::get('variable.CNF')
                ], Config::get('variable.CLIENT_ERROR'));
            }
            $country = $this->locationInterface->update('Country', $validateData, $id);
            return new CountryResource($country);
        } catch (\Exception $e) {
            return ResponseHelper::jsonResponseWithConfigError($e);
        }
    }

    // public function destroy(string $id)
    // {
    //     try {
    //         $country = $this->locationInterface->findById('Country', $id);
    //         if (!$country) {
    //             return response()->json([
    //                 'message' => Config::get('variable.CNF')
    //             ], Config::get('variable.CLIENT_ERROR'));
    //         }
    //         $deleted = $this->locationInterface->delete('Country', $id);
    //         if ($deleted) {
    //             return response()->noContent();
    //         } else {
    //             throw new \Exception(Config::get('variable.FTDC'));
    //         }
    //     } catch (\Exception $e) {
    //         return ResponseHelper::jsonResponseWithClientError($e);
    //     }
    // }

    public function destroy(String $id)
    {
        $country = $this->locationInterface->findById('Country', $id);
        if (!$country) {
            return response()->json([
                'message' => Config::get('variable.CNF')
            ], 401);
        }
        $country = $this->locationInterface->delete('Country', $id);
        return response()->json([
            'message' => Config::get('variable.CDF')
        ], Config::get('variable.NO_CONTENT'));
    }
}
