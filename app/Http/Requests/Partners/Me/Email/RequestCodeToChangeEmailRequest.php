<?php

namespace App\Http\Requests\Partners\Me\Email;

use Illuminate\Foundation\Http\FormRequest;

class RequestCodeToChangeEmailRequest extends FormRequest
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
