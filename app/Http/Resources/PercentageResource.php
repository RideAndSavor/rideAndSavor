<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PercentageResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'discount_percentage'=>$this->discount_percentage
        ];
    }

    public function with(Request $request)
    {
        return[
            'version' => '1.0.0',
            'api_url' => url('http://api.dailyfairdeal.com/api/percentage'),
            'message' => 'Your action is successful'
        ];
    }
}
