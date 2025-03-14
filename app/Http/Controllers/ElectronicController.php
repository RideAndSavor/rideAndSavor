<?php

namespace App\Http\Controllers;

use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use App\Services\ElectronicService;
use App\Http\Requests\ElectronicRequest;
use App\Http\Resources\ElectronicResource;

class ElectronicController extends BaseController
{
    use ImageTrait;
    protected $electronicService;
    protected $imageService;

    public function __construct(ElectronicService $electronicService,ImageService $imageService)
    {
        $this->electronicService = $electronicService;
        $this->imageService = $imageService;
    }

    /**
     * Get all travel records.
     */
    public function index(): JsonResponse
    {
        return $this->handleRequest(function () {
            $electronic = $this->electronicService->getAllelectronics();
            return response()->json(ElectronicResource::collection($electronic));
        });
    }

    /**
     * Store a new travel record.
     */
    // public function store(ElectronicRequest $request): JsonResponse
    // {

    //     $folder_name = 'public/eelctronic/';
    //     $tableName = 'images';

    //     return $this->handleRequest(function () use ($request) {
    //         $validateData = $request->validated();
    //         unset($validateData['upload_url']);

    //        // $electronic = $this->electronicService->store($validateData);
    //                                     //(Model $model, array $images, string $imageDir, string $genre )
    //         if ($request->hasFile('upload_url')) {
    //             $this->createImageTest($request, $electronic->id, $this->genre, $this->foodInterface, $folder_name, $tableName);
    //         }

    //         return response()->json([
    //             'electronic' => new ElectronicResource($electronic),

    //         ], 201);
    //     });
    // }

    public function store(ElectronicRequest $request): JsonResponse
    {
        $folder_name = 'electronic/'; // Fixed typo


        return $this->handleRequest(function () use ($request, $folder_name) {
            $validateData = $request->validated();
            $image[] = $validateData['upload_url'] ?? [];
            unset($validateData['upload_url']);

            // ✅ First, store the electronic item
        $electronic = $this->electronicService->store($validateData);

           // ✅ Then, handle image upload
            if ($request->hasFile('upload_url')) {
               // dd($request->hasFile('upload_url'));
                $this->createImageTest($electronic, $image, $folder_name, 'electronic');
            }

            return response()->json([
                'electronic' => new ElectronicResource($electronic),
            ], 201);
        });
    }


    /**
     * Update a travel record.
     */
    public function update(ElectronicRequest $request, $id): JsonResponse
    {
        return $this->handleRequest(function () use ($request, $id) {
            $travel = $this->electronicService->update($request->validated(), $id);
            return response()->json(new ElectronicResource($travel));
        });
    }

    /**
     * Delete a travel record.
     */
    public function destroy($id): JsonResponse
    {
        return $this->handleRequest(function () use ($id) {
            $this->electronicService->delete($id);
            return response()->json(['message' => 'Travel record deleted successfully'], 200);
        });
    }
}
