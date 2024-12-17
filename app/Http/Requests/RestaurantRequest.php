<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RestaurantRequest extends FormRequest
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

        // 
        return [ 
            'restaurant_type_id' => 'required|integer',
            'name' => 'required|string|max:255',
            'open_time' => 'required|date_format:g:i A', // 12-hour format with AM/PM
            'close_time' => 'required|date_format:g:i A', // 12-hour format with AM/PM
            'phone_number' => 'required|string|max:20',
            'addressData' => 'required|array',
            'addressData.street_id' => 'required|integer',
            'addressData.block_no' => 'required|string',
            'addressData.floor' => 'required|string',
            'addressData.latitude' => 'nullable|numeric',
            'addressData.longitude' => 'nullable|numeric',
        ];
    }
}
