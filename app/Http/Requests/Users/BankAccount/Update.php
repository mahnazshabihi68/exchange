<?php

namespace App\Http\Requests\Users\BankAccount;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $except = $this->bankAccount?->id;

        return [
            'sheba' => 'required|string|ir_sheba|unique:bank_accounts,sheba,' . $except,
            'card' => 'required|string|ir_bank_card_number|unique:bank_accounts,card,' . $except,
            'bank' => 'required|string',
        ];
    }
}
