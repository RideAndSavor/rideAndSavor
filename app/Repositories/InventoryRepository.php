<?php

namespace App\Repositories;

use App\Models\Inventory;
use App\Contracts\InventoryInterface;

class InventoryRepository extends BaseRepository implements InventoryInterface
{
    public function __construct()
    {
        parent::__construct(class_basename(Inventory::class));
    }

    public function updateStockStatus(int $inventoryId)
    {
        $inventory = Inventory::find($inventoryId);
        if ($inventory) {
            if ($inventory->remaining_stock <= 0) {
                $inventory->stock_status = 'out-of-stock';
            } elseif ($inventory->remaining_stock < 10) {
                $inventory->stock_status = 'low-stock';
            } else {
                $inventory->stock_status = 'in-stock';
            }
            $inventory->save();
            return $inventory;
        }
        return null;
    }
}
