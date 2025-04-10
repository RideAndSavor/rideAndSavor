<?php

namespace App\Http\Controllers;

use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Services\ImageService;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\ProductRequest;
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

    /**
     * Get all travel records.
     */
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
        $folder_name = 'product/'; // Fixed typo

        return $this->handleRequest(function () use ($request, $folder_name) {
            $validateData = $request->validated();
            $image[] = $validateData['upload_url'] ?? [];
            unset($validateData['upload_url']);

            // ✅ First, store the product item
        $product = $this->productService->store($validateData);

         // ✅ Retrieve category_id from subcategory relationship
         $shop_id = $validateData['shop_id'];
         $category_id = $product->subcategory->category_id; // ✅ Get category from subcategory

         // ✅ Store shop and category in the pivot table
         DB::table('shop_category')->updateOrInsert([
             'shop_id' => $shop_id,
             'category_id' => $category_id,
        ]);

           // ✅ Then, handle image upload
            if ($request->hasFile('upload_url')) {
                $this->createImageTest($product, $image, $folder_name, 'product');
            }

            return response()->json([
                'product' => new ProductResource($product),
            ], 201);
        });
    }


    /**
     * Update product.
     */
    // public function update(ProductRequest $request, $id): JsonResponse
    // {

    //     return $this->handleRequest(function () use ($request, $id) {
    //         $validatedData = $request->validated();

    //         // ✅ Update the product
    //         $product = $this->productService->update($validatedData, $id);

    //         // ✅ Update pivot table (shop_category)
    //         $shop_id = $validatedData['shop_id'];
    //         $category_id = $product->subcategory->category_id;
    //         DB::table('shop_category')->updateOrInsert([
    //             'shop_id' => $shop_id,
    //             'category_id' => $category_id,
    //         ]);

    //         return response()->json(new ProductResource($product));
    //     });
    // }


    public function update(ProductRequest $request, $id): JsonResponse
{
    $folder_name = 'product/';
    // dd($request->all());

    return $this->handleRequest(function () use ($request, $id, $folder_name) {

        $validatedData = $request->validated();
        // $validateData = $request->validated();
            $image[] = $validatedData['upload_url'] ?? [];
            unset($validatedData['upload_url']);

        $product = $this->productService->update($validatedData, $id);

        $shop_id = $validatedData['shop_id'];
        $category_id = $product->subcategory->category_id;

        DB::table('shop_category')->updateOrInsert([
            'shop_id' => $shop_id,
            'category_id' => $category_id,
        ]);

        if ($request->has('image_ids_to_delete')) {
            $imageIds = (array) $request->input('image_ids_to_delete'); // Cast to an array

            foreach ($product->images()->whereIn('id', $imageIds)->get() as $image) {
                $this->deleteImage($image->path);
                $image->delete();
            }
        }

        if ($request->hasFile('upload_url')) {
            $this->createImageTest($product, $image, $folder_name, 'product');
        }

        return response()->json(new ProductResource($product));
    });
}



    /**
     * Delete product.
     */
    public function destroy($id): JsonResponse
    {
        return $this->handleRequest(function () use ($id) {
            $this->productService->delete($id);
            return response()->json(['message' => 'Product deleted successfully'], 200);
        });
    }
}
