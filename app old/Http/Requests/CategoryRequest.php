<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    private const ALLOWED_EXTENSION = 'jpg,jpeg,png,webp,gif';

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return 1;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */

    public function rules()
    {
        // Get the current category ID for updating
        $currentCategoryId = $this->input('category_id', 0);

        return [
            'category_id' => ['sometimes', 'required', 'exists:category,category_id'],
            'category_name' => [
                'required',
                'string',
                'max:150',
                Rule::unique('category')->ignore($currentCategoryId, 'category_id')
            ],
            'category_image' => [
                $currentCategoryId ? 'nullable' : 'required',
                'image',
                'mimes:jpeg,jpg,png,gif'
            ]
        ];
    }
}
