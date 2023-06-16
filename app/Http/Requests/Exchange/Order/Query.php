<?php

namespace App\Http\Requests\Exchange\Order;

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
            'created_after' => 'nullable|string|date',
            'created_before' => 'nullable|date|string|after:created_after',
            'status' => 'nullable|array|in:' . implode(',', array_keys(__('attributes.exchange.orders.status'))),
            'side' => 'nullable|string',
            'type' => 'nullable|string',
            'is_virtual'    =>  'nullable|boolean'
        ];
    }
}
