<?php

declare(strict_types=1);

namespace App\Http\Requests\Backoffice\Auth;

use Illuminate\Foundation\Http\FormRequest;

class SignInRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => [
                'email',
                'required'
            ],
            'password' => [
                'string',
                'required'
            ],
        ];
    }
}
