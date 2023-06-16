<?php

namespace App\Http\Requests\Users\Profile;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
{

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'first_name' => 'string',
            'last_name' => 'string',
            'father_name' => 'nullable|string',
            'national_code' => 'nullable|ir_national_code|string|unique:users,national_code,' . $this->user('user')?->id,
            'birthday' => 'nullable|string',
            'email' => 'required|email|unique:users,email,' . $this->user('user')?->id,
            'mobile' => 'unique:users,mobile,' . $this->user('user')?->id,
            'avatar' => 'nullable|image|max:2048'
        ];
    }
}
