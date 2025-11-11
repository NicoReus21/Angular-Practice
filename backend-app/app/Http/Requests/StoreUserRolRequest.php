<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; 
    }

    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'rol_id' => 'required|exists:rols,id', 
        ];
    }
}