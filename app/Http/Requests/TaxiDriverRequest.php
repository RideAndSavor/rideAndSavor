<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TaxiDriverRequest extends FormRequest
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
            // 'rider_id' => 'required|exists:users,id',
            'current_location' => 'nullable|string|max:255',
            'is_available' => 'required|boolean',
            'car_year' => 'nullable|integer|min:1886|max:' . date('Y'),
            'car_make' => 'nullable|string|max:255',
            'car_model' => 'nullable|string|max:255',
            'car_colour' => 'nullable|string|max:50',
            'license_plate' => 'nullable|string|max:50',
            'other_info' => 'nullable|string',
        ];
    }
    
}
