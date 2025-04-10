<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ShopRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;  // Modify if you need authorization logic
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address_id' => 'required|exists:addresses,id',
            'description' => 'nullable|string',
            'website_url' => 'nullable|url',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'social_media_links' => 'nullable|array',
            'open_time' => 'nullable|string|max:10',
            'close_time' => 'nullable|string|max:10',
            'status' => 'nullable|in:active,inactive,suspended',
            'upload_url' => 'nullable|array',
            'upload_url.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'delete_image_ids' => 'nullable|array',
            'delete_image_ids.*' => 'exists:images,id',
        ];
    }

    /**
     * Get the error messages for the validation.
     *
     * @return array<string, string>
     */

}
