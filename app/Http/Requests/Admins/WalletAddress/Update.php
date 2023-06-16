<?php

namespace App\Http\Requests\Admins\WalletAddress;

use App\Rules\BlockchainAddressRule;
use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $except = $this->walletAddress?->id;

        return [
            'title' => 'required|string|unique:wallet_addresses,title,' . $except,
            'is_active' => 'required|boolean',
            'blockchain_id' => 'required|integer|exists:blockchains,id',
            'private_key'   =>  'nullable|string',
            'address' => ['required', 'string', 'unique:wallet_addresses,address,' . $except , 'address_is_valid:'. $this->request->get('blockchain_id')],
        ];
    }
}
