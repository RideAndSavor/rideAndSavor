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
            'latitude' => 'required|numeric|between:-90,90',  // Validating latitude directly
            'longitude' => 'required|numeric|between:-180,180',  // Validating longitude directly
            'is_available' => 'required|boolean',
            'car_year' => 'nullable|integer|min:1886|max:' . date('Y'),
            'car_make' => 'nullable|string|max:255',
            'car_model' => 'nullable|string|max:255',
            'car_colour' => 'nullable|string|max:50',
            'license_plate' => 'nullable|string|max:50',
            'driver_license_number' => 'nullable|string|max:100',
            'other_info' => 'nullable|string',
        ];
    }


}
