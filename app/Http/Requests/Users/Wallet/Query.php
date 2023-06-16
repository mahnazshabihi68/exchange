<?php

namespace App\Http\Requests\Users\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class Query extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'symbols' => 'array',
            'symbols.*' => 'string',
            'is_available' => 'boolean',
            'is_virtual' => 'boolean'
        ];
    }
}
