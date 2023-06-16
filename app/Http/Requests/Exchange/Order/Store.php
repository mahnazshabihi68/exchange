<?php

/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Requests\Exchange\Order;

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
            'market' => 'required|string|regex:/^([A-Z]*-[A-Z]*)+$/',
            'type' => 'required|string|in:' . implode(',', (unserialize(config('settings.accepted_order_types')))),
            'side' => 'required|string|in:BUY,SELL',
            'original_price' => 'nullable|required_unless:type,MARKET,OTC|numeric|gt:0',
            'original_quantity' => 'required|numeric|gt:0',
            'stop_price' => 'nullable|required_if:type,STOPLOSSLIMIT|numeric|gt:0',
            'is_virtual' => 'required|boolean'
        ];
    }
}
