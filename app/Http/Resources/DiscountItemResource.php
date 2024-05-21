<?php

namespace App\Http\Resources;

use App\Models\Percentage;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DiscountItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $percentage = Percentage::findOrFail($this->percentage_id);
        return [
            'percentage_id' => $this->percentage_id,
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'discount_percentage' => $percentage->discount_percentage

        ];
    }
}
