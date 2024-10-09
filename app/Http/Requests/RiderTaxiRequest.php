<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RiderTaxiRequest extends FormRequest
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
                'current_location' => 'required|array',  // Ensure it's an array
                'current_location.latitude' => 'required|numeric|between:-90,90',  // Latitude should be between -90 and 90
                'current_location.longitude' => 'required|numeric|between:-180,180',  // Longitude should be between -180 and 180
    
                'destination' => 'required|array',  // Ensure it's an array
                'destination.latitude' => 'required|numeric|between:-90,90',  // Latitude should be between -90 and 90
                'destination.longitude' => 'required|numeric|between:-180,180',  // Longitude should be between -180 and 180
            ];
    }
}
