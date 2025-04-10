<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Change based on your authorization logic
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            // 'shop_id' => 'required|exists:shops,id',
            'subcategory_id' => 'required|exists:sub_categories,id',
            'brand_id' => 'required|exists:brands,id',
            'original_price' => 'required|numeric|min:0',
            'discount_percent' => 'nullable|numeric|min:0',
            // 'final_price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'weight' => 'nullable|numeric|min:0',
            'color' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|in:available,unavailable',
            'upload_url' => 'nullable|array',
            'upload_url.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }


}
