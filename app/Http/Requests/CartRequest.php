<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
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
            'cart' => ['required', 'array'],
            'cart.total_price' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
            'cart_item' => ['required', 'array'],
            'cart_item.*.food_id' => ['required', 'integer'],
            'cart_item.*.restaurant_id' => ['required', 'integer'],
            'cart_item.*.quantity' => ['required'],
            'cart_item.*.price' => ['required', 'numeric', 'regex:/^\d+(\.\d{1,2})?$/'],
        ];
    }
}
