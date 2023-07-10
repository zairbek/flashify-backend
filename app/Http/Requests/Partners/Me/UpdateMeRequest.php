<?php

namespace App\Http\Requests\Partners\Me;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'firstName' => ['string', 'nullable'],
            'lastName' => ['string', 'nullable'],
        ];
    }
}
