<?php

namespace App\Http\Requests\Backoffice\Auth;

use Illuminate\Foundation\Http\FormRequest;

class RefreshingTokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'refreshToken' => ['string', 'required']
        ];
    }
}
