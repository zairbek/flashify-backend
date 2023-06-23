<?php

namespace App\Http\Requests\Partners\Auth\Email;

use Illuminate\Foundation\Http\FormRequest;

class RequestCodeToEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['email', 'required']
        ];
    }
}
