<?php

namespace App\Http\Requests\Admins\Symbol;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $except = $this->symbol?->id;

        return [
            'title' => 'required|string|unique:symbols,title,' . $except,
            'name_en' => 'required|string|unique:symbols,name_en,' . $except,
            'name_fa' => 'required|string|unique:symbols,name_fa,' . $except,
            'picture' => 'nullable|image|max:500',
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
