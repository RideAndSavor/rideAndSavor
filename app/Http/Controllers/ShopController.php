<?php

namespace App\Http\Controllers;

use App\Models\Shop;
use App\Traits\ImageTrait;
use App\Services\ShopService;
use App\Services\ImageService;
use App\Http\Requests\ShopRequest;
use App\Http\Resources\ShopResource;
use Illuminate\Support\Facades\Auth;
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

    public function store(ShopRequest $shopRequest)
    {
        // dd($shopRequest->validated());
        $folder_name = 'shop/';

        return $this->handleRequest(function () use ($shopRequest, $folder_name) {
            $validateData = $shopRequest->validated();
            $image[] = $validateData['upload_url'] ?? [];
            unset($validateData['upload_url']);

            $validateData['user_id'] = Auth::id();
            $shop = $this->shopService->store($validateData);

            if ($shopRequest->hasFile('upload_url')) {
                 $this->createImageTest($shop, $image, $folder_name, 'shop');
             }
             $shop = $shop->load('images', 'address');

            return response()->json(new ShopResource($shop),Config::get('variable.CREATED'));
        });
    }


    public function update(ShopRequest $shopRequest, Shop $shop)
    {
        $folder_name = 'shop/';

        return $this->handleRequest(function () use ($shopRequest, $shop, $folder_name) {
            $validateData = $shopRequest->validated();
            $images = $validateData['upload_url'] ?? [];
            $deleteImageIds = $validateData['delete_image_ids'] ?? [];
            unset($validateData['upload_url'], $validateData['delete_image_ids']);

            $shop->update($validateData);

            $this->updateImageTest($shop, $images, $deleteImageIds, $folder_name, 'shop');
            $shop = $shop->load('images', 'address');

            return response()->json(new ShopResource($shop), Config::get('variable.OK'));
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
