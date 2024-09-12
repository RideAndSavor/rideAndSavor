<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentProviderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'paymentprovider' => $this->paymentmode
        ];
    }

    public function with(Request $request)
    {
        return [
            'version' => '1.0.0',
            'api_url' => url('http://api.dailyfairdeal.com/api/paymentmode'),
            'message' => "You are action is successful!"
        ];
    }
}
