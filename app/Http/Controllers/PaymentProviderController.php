<?php

namespace App\Http\Controllers;

use App\Contracts\PaymentProviderInterface;
use App\Http\Requests\PaymentProviderRequest;
use App\Http\Resources\PaymentProviderResource;
use App\Models\PaymentProvider;
use Illuminate\Support\Facades\Config;

class PaymentProviderController extends Controller
{
    private $paymentModeInterface;
    public function __construct(PaymentProviderInterface $paymentModeInterface)
    {
        $this->paymentModeInterface = $paymentModeInterface;
    }
    public function index()
    {
        $paymentProvider = $this->paymentModeInterface->all('PaymentProvider');
        return PaymentProviderResource::collection($paymentProvider);
    }

    public function store(PaymentProviderRequest $paymentModeRequest)
    {
        $validatedData =  $paymentModeRequest->validated();
        $paymentProviderData = $this->paymentModeInterface->store('PaymentProvider', $validatedData);
        if (!$paymentProviderData) {
            return response()->json([
                'message' => Config::get('variable.FAILED_TO_CREATE_PAYMENT_PROVIDER')
            ], Config::get('variable.CLIENT_ERROR'));
        }
        return new PaymentProviderResource($paymentProviderData);
    }

    public function update(PaymentProviderRequest $request, $id)
    {
        $validatedData = $request->validated();
        $paymentProvider = $this->paymentModeInterface->findByID('PaymentProvider', $id);
        if (!$paymentProvider) {
            return response()->json([
                'message' =>Config::get('variable.PAYMENT_PROVIDER_NOT_FOUND')
            ], Config::get('variable.SEVER_ERROR'));
        }
        $paymentProviderData = $this->paymentModeInterface->update('PaymentProvider', $validatedData, $id);
        return new PaymentProviderResource($paymentProviderData);
    }


    public function destroy(string $id)
    {
        $paymentProvider = $this->paymentModeInterface->findByID('PaymentProvider', $id);
        if (!$paymentProvider) {
            return response()->json([
                'message' => Config::get('variable.PAYMENT_PROVIDER_NOT_FOUND')
            ], Config::get('variable.SEVER_ERROR'));
        }
        $this->paymentModeInterface->delete('PaymentProvider', $id);
        return response()->json([
            'message' =>Config::get('variable.PAYMENT_PROVIDER_DELETED_SUCCESSFULLY')
        ], Config::get('variable.NO_CONTENT'));
    }
}
