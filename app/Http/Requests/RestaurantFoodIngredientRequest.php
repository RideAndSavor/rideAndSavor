<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantFoodIngredientRequest extends FormRequest
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
            'food' => 'required|array',
            'food.sub_category_id' => 'nullable|integer',
            'food.name' => 'required|string|max:255',
            'food.quantity' => 'nullable|string|max:255',
            'ingredients' => 'required|array',
            'ingredients.*.name' => 'required|string|max:255'
        ];
    }
}
