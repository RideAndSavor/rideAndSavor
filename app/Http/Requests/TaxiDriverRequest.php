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
            'current_location' => 'nullable|array',
            'current_location.latitude' => 'required_with:current_location|numeric|between:-90,90',
            'current_location.longitude' => 'required_with:current_location|numeric|between:-180,180',
            'is_available' => 'required|boolean',
            'car_year' => 'nullable|integer|min:1886|max:' . date('Y'),
            'car_make' => 'nullable|string|max:255',
            'car_model' => 'nullable|string|max:255',
            'car_colour' => 'nullable|string|max:50',
            'license_plate' => 'nullable|string|max:50',
            'driver_license_number' => 'nullable|string|max:100', // Adding driver license number validation
            'other_info' => 'nullable|string',
        ];
    }


}
