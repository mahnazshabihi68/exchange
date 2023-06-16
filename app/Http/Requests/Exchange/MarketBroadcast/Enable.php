<?php

namespace App\Http\Requests\Exchange\MarketBroadcast;

use Illuminate\Foundation\Http\FormRequest;

class Enable extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'market' => 'required|string',
            'queue' => 'required|string|in:mainnet,testnet'
        ];
    }
}
