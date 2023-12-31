<?php

namespace App\Http\Requests\Backoffice\Categories;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['string', 'required'],
            'slug' => ['string', 'required'],
            'description' => ['string', 'nullable'],
            'parentCategory' => ['string', 'nullable'],
            'active' => ['boolean', 'nullable'],
            'icon' => ['string', 'nullable'],
        ];
    }
}
