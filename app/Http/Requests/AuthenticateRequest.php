<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AuthenticateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'token' => 'required|string',
        ];
    }
}
