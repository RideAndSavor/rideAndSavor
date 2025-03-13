<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrandRequest;
use App\Http\Resources\BrandResource;
use App\Services\BrandService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Auth;

class BrandController extends BaseController
{
    protected $brandService;

    public function __construct(BrandService $brandService)
    {
        $this->brandService = $brandService;
    }

    public function index()
    {
        return $this->handleRequest(function () {
            $brands = $this->brandService->getAllBrands();
            return response()->json(BrandResource::collection($brands)->toArray(request()), Config::get('variable.OK'));
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(BrandRequest $brandRequest)
{
    return $this->handleRequest(function () use ($brandRequest) {
        $validatedData = $brandRequest->validated();
        $brand = $this->brandService->store($validatedData);

        // Wrap the TaxiDriverResource in a JsonResponse
        return response()->json(new BrandResource($brand),Config::get('variable.CREATED'));
    });
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->handleRequest(function () use ($id) {
            $brand = $this->brandService->getById($id);

            if (!$brand) {
                return response()->json(['message' => Config::get('variable.BRAND_NOT_FOUND')],Config::get('variable.SEVER_NOT_FOUND'));
            }

            return new BrandResource($brand);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(BrandRequest $request, string $id)
    {
        // dd("OK");
        return $this->handleRequest(function () use ($request, $id) {
            $validatedData = $request->validated();
            $brand = $this->brandService->update($validatedData, $id);

            if (!$brand) {
                return response()->json(['message' => Config::get('variable.BRAND_NOT_FOUND')], status: Config::get('variable.SEVER_NOT_FOUND'));
            }

            return response()->json(new BrandResource($brand), Config::get('variable.OK'));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->handleRequest(function () use ($id) {
            $this->brandService->delete($id);
            return response()->json(['message' => Config::get('variable.BRAND_DELETED_SUCCESSFULLY')], 200);
        });
    }
}
