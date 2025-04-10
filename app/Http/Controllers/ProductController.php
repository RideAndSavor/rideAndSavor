<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Traits\ImageTrait;
use App\Services\ImageService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\ProductRequest;
use Illuminate\Support\Facades\Config;
use App\Http\Resources\ProductResource;

class ProductController extends BaseController
{
    use ImageTrait;
    protected $productService;
    protected $imageService;

    public function __construct(ProductService $productService,ImageService $imageService)
    {
        $this->productService = $productService;
        $this->imageService = $imageService;
    }

    public function index(): JsonResponse
    {
        return $this->handleRequest(function () {
            $products = $this->productService->getAllproducts();
            return response()->json(ProductResource::collection($products));
        });
    }

    public function show($id): JsonResponse
    {
        return $this->handleRequest(function () use ($id) {
            $product = $this->productService->getById($id);
            return response()->json(new ProductResource($product));
        });
    }

    public function store(ProductRequest $request): JsonResponse
    {
        $folder_name = 'product/';

        return $this->handleRequest(function () use ($request, $folder_name) {
            $shopId = Auth::user()->shop->id;
            $validateData = $request->validated();
            $image[] = $validateData['upload_url'] ?? [];
            unset($validateData['upload_url']);

            $validateData['shop_id'] = $shopId;
            if (isset($validateData['discount_percent']) && $validateData['discount_percent'] > 0) {
                $validateData['final_price'] = $validateData['original_price'] * (1 - ($validateData['discount_percent'] / 100));
            } else {
                $validateData['final_price'] = $validateData['original_price'];
            }

            $product = $this->productService->store($validateData);

            $shop_id = $validateData['shop_id'];
            $category_id = $product->subcategory->category_id;
            DB::table('shop_category')->updateOrInsert([
                'shop_id' => $shop_id,
                'category_id' => $category_id,
            ]);

            if ($request->hasFile('upload_url')) {
                $this->createImageTest($product, $image, $folder_name, 'product');
            }

            $product = $product->load('shop', 'subcategory', 'brand', 'images');
            return response()->json([
                'product' => new ProductResource($product),
            ], 201);
        });
    }


    public function update(ProductRequest $request, $id): JsonResponse
    {
        return $this->handleRequest(function () use ($request, $id) {
            $shopId = Auth::user()->shop->id;

            $product = $this->productService->getById($id);
            $validatedData = $request->validated();
            $image[] = $validatedData['upload_url'] ?? [];
            unset($validatedData['upload_url']);

            $validatedData['shop_id'] = $shopId;

            if (isset($validatedData['discount_percent']) && $validatedData['discount_percent'] > 0) {
                $validatedData['final_price'] = $validatedData['original_price'] * (1 - ($validatedData['discount_percent'] / 100));
            } else {
                $validatedData['final_price'] = $validatedData['original_price'];
            }
            $product = $this->productService->update($validatedData, $id);

            $category_id = $product->subcategory->category_id;
            DB::table('shop_category')->updateOrInsert([
                'shop_id' => $shopId,
                'category_id' => $category_id,
            ]);

            if ($request->hasFile('upload_url')) {
                $this->createImageTest($product, $image, 'product/', 'product');
            }

            $product = $product->load('shop', 'subcategory', 'brand', 'images');

            return response()->json([
                'product' => new ProductResource($product),
            ], 200);
        });
    }

    public function destroy($id): JsonResponse
    {
        return $this->handleRequest(function () use ($id) {
            $this->productService->delete($id);
            return response()->json(['message' => 'Product deleted successfully'], 200);
        });
    }
}
