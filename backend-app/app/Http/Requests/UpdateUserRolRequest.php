<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    
    public function rules(): array
    {
        return [
            'user_id' => 'sometimes|required|exists:users,id',
            'rol_id' => 'sometimes|required|exists:rols,id',
        ];
    }
}