<?php

namespace App\Http\Requests\Users\BankAccount;

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
            'sheba' => 'required|ir_sheba|string|unique:bank_accounts,sheba',
            'card' => 'required|ir_bank_card_number|string|unique:bank_accounts,card',
            'bank' => 'required|string',
        ];
    }
}
