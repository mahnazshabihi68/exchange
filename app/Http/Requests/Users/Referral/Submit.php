<?php

namespace App\Http\Requests\Users\Referral;

use Illuminate\Foundation\Http\FormRequest;

class Submit extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => 'required|string'
        ];
    }
}
