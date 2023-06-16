<?php

namespace App\Http\Requests\Admins\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class Query extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'query_type' => 'required|string|in:deposit,withdraw,order',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'users' => 'nullable|array',
        ];
    }
}
