<?php

namespace App\Http\Controllers;

use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Services\ShopService;
use App\Services\BrandService;
use App\Services\ImageService;
use App\Http\Requests\ShopRequest;
use App\Http\Requests\BrandRequest;
use App\Http\Resources\ShopResource;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\BrandResource;
use Illuminate\Support\Facades\Config;

class ShopController extends BaseController
{
    use ImageTrait;
    protected $shopService;
    protected $imageService;

    public function __construct(ShopService $shopService,ImageService $imageService)
    {
        $this->shopService = $shopService;
        $this->imageService = $imageService;
    }

    public function index()
    {
        return $this->handleRequest(function () {
            $shops = $this->shopService->getAllShops();
            return response()->json(ShopResource::collection($shops)->toArray(request()), Config::get('variable.OK'));
        });
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(ShopRequest $shopRequest)
    {

        $folder_name = 'shop/'; // Fixed typo

        return $this->handleRequest(function () use ($shopRequest, $folder_name) {
            $validateData = $shopRequest->validated();
            $image[] = $validateData['upload_url'] ?? [];
            unset($validateData['upload_url']);

            $validateData['user_id'] = Auth::id();
            $shop = $this->shopService->store($validateData);

            // ✅ Then, handle image upload
            if ($shopRequest->hasFile('upload_url')) {
                // dd($request->hasFile('upload_url'));
                 $this->createImageTest($shop, $image, $folder_name, 'shop');
             }

            // Wrap the TaxiDriverResource in a JsonResponse
            return response()->json(new ShopResource($shop),Config::get('variable.CREATED'));
        });
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return $this->handleRequest(function () use ($id) {
            $shop = $this->shopService->getById($id);

            if (!$shop) {
                return response()->json(['message' => Config::get('variable.SHOP_NOT_FOUND')],Config::get('variable.SEVER_NOT_FOUND'));
            }

            return new ShopResource($shop);
        });
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(ShopRequest $request, string $id)
    {
        // dd("OK");
        return $this->handleRequest(function () use ($request, $id) {
            $validatedData = $request->validated();
            $shop = $this->shopService->update($validatedData, $id);

            if (!$shop) {
                return response()->json(['message' => Config::get('variable.SHOP_NOT_FOUND')], status: Config::get('variable.SEVER_NOT_FOUND'));
            }

            return response()->json(new BrandResource($shop), Config::get('variable.OK'));
        });
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return $this->handleRequest(function () use ($id) {
            $this->shopService->delete($id);
            return response()->json(['message' => Config::get('variable.SHOP_DELETED_SUCCESSFULLY')], 200);
        });
    }
}
