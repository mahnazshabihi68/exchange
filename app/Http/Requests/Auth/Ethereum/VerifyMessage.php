<?php

namespace App\Http\Requests\Auth\Ethereum;

use Illuminate\Foundation\Http\FormRequest;

class VerifyMessage extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'address'   =>  'required|string',
            'signature'   =>  'required|string',
            'nonce' =>  'required|string'
        ];
    }
}
