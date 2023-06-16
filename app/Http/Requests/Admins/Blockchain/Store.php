<?php

namespace App\Http\Requests\Admins\Blockchain;

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
            'title' => 'required|string|unique:blockchains,title',
            'name_fa' => 'required|string|unique:blockchains,name_fa',
            'name_en' => 'required|string|unique:blockchains,name_en',
            'picture'   =>  'nullable|image|max:1024',
            'deposit_min_needed_confirmations' => 'required|integer|gt:0',
            'symbols' => 'nullable|array',
            'symbols.*.symbol_id' => 'required|integer|exists:symbols,id',
            'symbols.*.transfer_fee' => 'required|numeric|gte:0'
        ];
    }
}
