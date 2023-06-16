<?php

namespace App\Http\Requests\Admins\Market;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'market' => 'required|string',
            'is_active' => 'required|boolean',
            'is_direct' => 'required|boolean',
            'is_internal' => 'required|boolean',
        ];
    }
}
