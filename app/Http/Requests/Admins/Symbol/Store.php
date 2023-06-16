<?php

namespace App\Http\Requests\Admins\Symbol;

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
            'title' => 'required|string|unique:symbols,title',
            'name_en' => 'required|string',
            'name_fa' => 'required|string',
            'picture' => 'required|image|max:500',
            'is_withdrawable' => 'required|boolean',
            'is_depositable' => 'required|boolean',
            'min_withdrawable_quantity' => 'required|numeric|gt:0',
            'max_withdrawable_quantity' => 'required|numeric|gt:0',
            'precision' => 'required|integer|gte:0',
            'blockchains' => 'nullable|array',
            'blockchains.*.blockchain_id' => 'required|integer|exists:blockchains,id',
            'blockchains.*.transfer_fee' => 'required|numeric|gt:0'
        ];
    }
}
