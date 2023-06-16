<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Requests\Auth\OTP;

use Illuminate\Foundation\Http\FormRequest;

class Send extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'credential' => 'required|string'
        ];
    }
}
