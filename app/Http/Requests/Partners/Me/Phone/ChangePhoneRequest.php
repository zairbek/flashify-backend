<?php

declare(strict_types=1);

namespace App\Http\Requests\Partners\Me\Phone;

use Illuminate\Foundation\Http\FormRequest;

class ChangePhoneRequest extends FormRequest
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
            ],
            'code' => [
                'string',
                'max:6',
                'required'
            ],
        ];
    }
}
