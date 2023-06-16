<?php

namespace App\Http\Requests\Users\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class WithdrawRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|string|in:crypto,fiat',
            'quantity' => 'required|numeric|gt:0',
            'symbol_id' => 'required|integer|exists:symbols,id',
            'blockchain_id' => 'nullable|required_if:type,crypto|integer|exists:blockchains,id',
            'bankAccount_id' => 'nullable|required_if:type,fiat|integer',
            'destination_wallet_address' => ['nullable','required_if:type,crypto','string', 'address_is_valid:'. $this->request->get('blockchain_id')],
        ];
    }
}
