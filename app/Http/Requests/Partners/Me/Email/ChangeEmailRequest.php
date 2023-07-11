<?php

declare(strict_types=1);

namespace App\Http\Requests\Partners\Me\Email;

use Illuminate\Foundation\Http\FormRequest;

class ChangeEmailRequest extends FormRequest
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
            'code' => [
                'numeric',
                'required'
            ],
        ];
    }
}
