<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule; 

class UpdateRolRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // 1. Cambia esto a true
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        $rolId = $this->route('rol')->id;

        return [
            'name' => [
                'required',
                'string',
                Rule::unique('rols')->ignore($rolId),
            ],
        ];
    }
}