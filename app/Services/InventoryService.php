<?php

namespace App\Services;

use App\Contracts\InventoryInterface;
use App\Models\Inventory;

class InventoryService
{
    private $inventoryRepository;

    public function __construct(InventoryInterface $inventoryRepository)
    {
        $this->inventoryRepository = $inventoryRepository;
    }

    public function getAllInventories()
    {
      return $this->inventoryRepository->all();
    }

    public function store(array $data): Inventory
    {
        return $this->inventoryRepository->store($data);
    }

    public function update(array $data, int $id)
    {
        return $this->inventoryRepository->update($data, $id);
    }

    public function delete(int $id)
    {
        $this->inventoryRepository->delete($id);
    }

    // public function deleteByTravelId(int $travelId)
    // {
    //     return $this->inventoryRepository->deleteByTravelId($travelId);
    // }

    public function updateStock(int $inventoryId)
    {
        return $this->inventoryRepository->updateStockStatus($inventoryId);
    }
}
