<?php

namespace App\Http\Requests\Admins\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class WithdtrawUpdate extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules()
    {
        return [
            'public_key'    => 'nullable|string|required_if:type,crypto',
            'private_key'   => 'nullable|string|required_if:type,crypto',
            'provider'      => 'required_if:type,crypto',
            'is_approved' => 'required|boolean',
//            'ref' => 'string|required_if:is_approved,1|unique:withdraws',
            'reject_reason' => 'string|required_if:is_approved,0'
        ];
    }
}
