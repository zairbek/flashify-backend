<?php

namespace App\Http\Requests\Backoffice\Digest\Icon;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class CreateIconRequest extends FormRequest
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
                'required',
                File::types(['svg'])
            ],
        ];
    }
}
