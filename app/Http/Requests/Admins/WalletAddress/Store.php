<?php

namespace App\Http\Requests\Admins\WalletAddress;

use App\Rules\BlockchainAddressRule;
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
            'title' => 'required|string|unique:wallet_addresses',
            'is_active' => 'required|boolean',
            'blockchain_id' => 'required|integer|exists:blockchains,id',
            'address' => ['required', 'string', 'unique:wallet_addresses', 'address_is_valid:'. $this->request->get('blockchain_id')],
            'private_key' => 'nullable|string'
        ];
    }
}
