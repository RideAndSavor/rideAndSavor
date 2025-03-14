<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'electronic_id' => $this->electronic_id,
            'in_stock' => $this->in_stock,
            'out_stock' => $this->out_stock,
            'remaining_stock' => $this->remaining_stock,
            'stock_status' => $this->stock_status,
            'remarks' => $this->remarks,
        ];
    }

    public function with(Request $request)
    {
        return [
            'version' => '1.0.0',
            'api_url' => url('http://api.dailyfairdeal.com/api/inventory'),
            'message' => 'Inventory data fetched successfully'
        ];
    }
}
