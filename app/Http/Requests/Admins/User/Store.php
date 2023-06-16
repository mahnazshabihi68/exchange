<?php


/******************************************************************************
 *                                                                            *
 *  This project is not free and has business trademarks.                     *
 *  Ali Khedmati | +989122958172 | Ali@khedmati.ir                            *
 *  Copyright (c)  2020-2022, Ali Khedmati Co.                                *
 *                                                                            *
 ******************************************************************************/

namespace App\Http\Requests\Admins\User;

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
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'father_name' => 'string',
            'national_code' => 'string|ir_national_code|unique:users,national_code',
            'birthday' => 'string',
            'email' => 'email|unique:users,email',
            'mobile' => 'required|ir_mobile|unique:users,mobile',
            'avatar' => 'image|max:2048',
            'permissions' => 'array',
            'groups' => 'array',
            'password' => 'required|string|min:6|confirmed',
        ];
    }
}
