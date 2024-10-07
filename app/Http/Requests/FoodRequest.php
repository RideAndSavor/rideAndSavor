<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FoodRequest extends FormRequest
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
            'topping_id' => 'required|array',
            'topping_id.*' => 'nullable|integer|exists:toppings,id',
            'name'=>'required|string',
            // 'quantity'=>'required|string',
            'sub_category_id'=>'required|integer',
            'upload_url'=>'sometimes|required',
            'upload_url.*' => 'mimes:jpeg,png,jpg,gif,svg|max:2048' ,
        ];
    }
}
