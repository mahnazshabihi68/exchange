<?php

namespace App\Http\Requests\Admins\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class DepositStore extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'user_id' => 'required|numeric|exists:users,id',
            'symbol_id' => 'required|integer|exists:symbols,id',
            'quantity' => 'required|numeric|gt:0',
            'ref' => 'required|string|unique:deposits'
        ];
    }
}
