<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'user_id'=>'required|integer',
            'status_id'=>'required|integer',
            'delivery_price_id'=>'required|integer',
            'total_amount'=>'required|numeric|between:0,99999999.99',
            'total_discount_amount'=>'required|numeric|between:0,99999999.99',
            'comment'=>'required|string'
         ];
    }
}
