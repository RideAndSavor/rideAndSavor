<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AcceptDriverRequest extends FormRequest
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
            // 'user_id' => 'required|exists:users,id', // Ensure the user_id exists in the 'users' table
            'travel_id' => 'required|exists:travels,id',  // Ensure the travel_id exists in the 'travels' table
            'taxi_driver_id' => 'required|exists:users,id', // Ensure the taxi_driver_id exists in the 'users' table (assuming taxi drivers are stored in the users table)
            'price' => 'required|numeric|min:0', // Ensure the price is a valid numeric value
        ];
    }
}
