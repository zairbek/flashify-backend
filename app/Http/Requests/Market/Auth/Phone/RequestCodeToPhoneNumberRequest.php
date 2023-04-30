<?php

namespace App\Http\Requests\Market\Auth\Phone;

use Illuminate\Foundation\Http\FormRequest;

class RequestCodeToPhoneNumberRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => [
                'regex:/(^\+996)((\d{9})|(\s\(\d{3}\)\s\d{3}\s\d{2}\s\d{2}))/',
                'required'
            ]
        ];
    }
}
