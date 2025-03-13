<?php

namespace App\Contracts;

interface InventoryInterface extends BaseInterface
{
    public function updateStockStatus(int $inventoryId);
}

