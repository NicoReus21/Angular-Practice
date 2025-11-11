<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'module' => 'required|string|max:255',
            'section' => 'required|string|max:255',
            'action' => ['required', new Enum(['create', 'read', 'update', 'delete'])],
            'description' => 'nullable|string',
        ];
    }
}