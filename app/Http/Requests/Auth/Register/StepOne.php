<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Requests\Auth\Register;

use Illuminate\Foundation\Http\FormRequest;

class StepOne extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'credential' => 'required|email|string',
            'captcha_key' => 'required|string',
            'captcha' => 'required|captcha_api:' . request('captcha_key') . ',default',
            'agreement' => 'required|boolean'
        ];
    }
}
