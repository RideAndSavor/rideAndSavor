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
            'order' => ['required', 'array'],
            'order.cart_id' => ['required', 'integer'],
            'order.delivery_price_id' => ['required', 'integer'],
            'order.total_amount' => ['required', 'numeric'],
            'order.total_discount_amount' => ['required', 'numeric'],
            'order.comment' => ['nullable', 'string'],
            'order_item' => ['required', 'array'],
            'order_item.*.food_restaurant_id' => ['required', 'integer'],
            'order_item.*.quantity' => ['required', 'integer'],
            'order_item.*.price' => ['required', 'numeric'],
            'order_item.*.discount_prices' => ['required', 'numeric']
        ];
    }
}
