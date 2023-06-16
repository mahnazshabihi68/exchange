<?php

namespace App\Http\Requests\Admins\Notification;

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
            'title' => 'required|string',
            'content' => 'required',
            'send_method' => 'required|string|in:users,groups',
            'users' => 'nullable|required_if:send_method,users|array',
            'groups' => 'nullable|required_if:send_method,groups|array',
            'is_highlighted' => 'required|boolean'
        ];
    }
}
