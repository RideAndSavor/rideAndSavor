<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'electronic_id' => 'required|integer|exists:electronics,id',
            'in_stock' => 'required|integer|min:0',
            'out_stock' => 'required|integer|min:0',
            'remaining_stock' => 'required|integer|min:0',
            'stock_status' => 'nullable|in:in-stock,low-stock,out-of-stock',
            'remarks' => 'nullable|string',
        ];
    }
}
