<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthRequest extends FormRequest
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
    // public function rules(): array
    // {
    //     return [
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users,email',
    //         'password' => 'required|string|min:6',
    //         'phone_no' => 'sometimes|required|string|max:11|unique:users,phone_no',
    //         'role' => 'string|max:100',
    //         'gender' => 'sometimes|required|string|in:male,female,other',
    //         'age' => 'sometimes|required|integer|min:1|max:120'
    //     ];
    // }

    public function rules(): array
    {
        // 
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|string|min:6',
        ];
    }

}
