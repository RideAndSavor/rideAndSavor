<?php

namespace App\Http\Controllers;

use App\Http\Requests\InventoryRequest;
use App\Http\Resources\InventoryResource;
use App\Services\InventoryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class InventoryController extends BaseController
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Get all inventory items.
     */
    public function index()
    {
        return $this->handleRequest(function () {
            $inventory = $this->inventoryService->getAllInventories();
            return response()->json(InventoryResource::collection($inventory)->toArray(request()), Config::get('variable.OK'));
        });
    }

    /**
     * Store a new inventory item.
     */
    public function store(InventoryRequest $request)
    {
        return $this->handleRequest(function () use ($request) {
            $validatedData = $request->validated();
            $inventory = $this->inventoryService->store($validatedData);

            return response()->json(new InventoryResource($inventory), Config::get('variable.CREATED'));
        });
    }

    /**
     * Update an inventory item.
     */
    public function update(InventoryRequest $request, $id)
    {
        return $this->handleRequest(function () use ($request, $id) {
            $validatedData = $request->validated();
            $inventory = $this->inventoryService->update($validatedData, $id);

            if (!$inventory) {
                return response()->json(['message' => Config::get('variable.INVENTORY_NOT_FOUND')], Config::get('variable.SEVER_NOT_FOUND'));
            }

            return response()->json(new InventoryResource($inventory), Config::get('variable.OK'));
        });
    }

    /**
     * Delete an inventory item.
     */
    public function destroy($id)
    {
        return $this->handleRequest(function () use ($id) {
            $this->inventoryService->delete($id);
            return response()->json(['message' => Config::get('variable.INVENTORY_DELETED_SUCCESSFULLY')], Config::get('variable.NO_CONTENT'));
        });
    }

    /**
     * Update inventory stock.
     */
    public function updateStock($id)
    {
        return $this->handleRequest(function () use ($id) {
            $inventory = $this->inventoryService->updateStock($id);

            if (!$inventory) {
                return response()->json(['message' => Config::get('variable.INVENTORY_NOT_FOUND')], Config::get('variable.SEVER_NOT_FOUND'));
            }

            return response()->json(new InventoryResource($inventory), Config::get('variable.OK'));
        });
    }
}
