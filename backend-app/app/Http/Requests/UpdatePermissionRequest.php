<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;

class UpdatePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'module' => 'sometimes|required|string|max:255',
            'section' => 'sometimes|required|string|max:255',
            'action' => ['sometimes', 'required', new Enum(['create', 'read', 'update', 'delete'])],
            'description' => 'nullable|string',
        ];
    }
}