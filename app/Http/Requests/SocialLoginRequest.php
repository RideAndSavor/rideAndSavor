<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialLoginRequest extends FormRequest
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
            'code' => ['required', 'string'], // Validate as required query parameter
            'provider' => ['nullable', 'string', 'alpha'], // Optional provider parameter
        ];
        // return [
        //     'code' => ['bail', 'required', 'string'],
        //     'provider' => ['required', 'string', 'alpha']
        // ];
    }
}
