<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantFoodToppingRequest extends FormRequest
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
            'food' => 'required|array', // Validation for food
            'food.sub_category_id' => 'nullable|integer',
            'food.name' => 'required|string|max:255',
            'food.quantity' => 'nullable|string|max:255',
            'toppings' => 'required|array', // Validation for toppings
            'toppings.*.price' => 'required|string',
            'toppings.*.name' => 'required|string|max:255',
            'food_restaurant' => 'required|array', // Validation for food_restaurant
            'food_restaurant.*.price' => 'required|string',
            'food_restaurant.*.size_id' => 'required|integer',
            'discount_item_id' => 'nullable|integer',
            'upload_url.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg', // uploading image for food
        ];
    }
}
