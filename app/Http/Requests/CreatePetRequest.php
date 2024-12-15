<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreatePetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'status' => 'required|in:available,pending,sold',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Pet name is required.',
            'status.in' => 'Status must be one of: available, pending, sold.',
        ];
    }
}
