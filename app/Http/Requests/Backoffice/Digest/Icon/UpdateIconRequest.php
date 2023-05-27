<?php

namespace App\Http\Requests\Backoffice\Digest\Icon;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateIconRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['string', 'required'],
            'file' => [
                'nullable',
                File::types(['svg'])
            ],
        ];
    }
}
