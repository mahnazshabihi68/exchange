<?php

namespace App\Http\Requests\Users\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class DepositRequest extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|string|in:fiat,crypto',
            'quantity' => 'nullable|required_if:type,fiat|integer|between:' . config('settings.irt_deposit_min_amount') . ',' . config('settings.irt_deposit_max_amount'),
            'symbol_id' => 'nullable|required_if:type,crypto|integer|exists:symbols,id',
            'blockchain_id' => 'nullable|required_if:type,crypto|integer|exists:blockchains,id',
        ];
    }
}
