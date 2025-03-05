<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BiddingPriceRequest extends FormRequest
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
            'travel_id' => 'required|exists:travels,id',  // Ensure the travel_id exists in the 'travels' table
            'taxi_driver_id' => 'required|exists:taxi_drivers,id', // Ensure the taxi_driver_id exists in the 'taxi_drivers' table
            'price' => 'required|numeric',// Ensure the price is a valid numeric value
        ];
    }
}
