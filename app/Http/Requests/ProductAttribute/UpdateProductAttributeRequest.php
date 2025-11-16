<?php

declare(strict_types=1);

namespace App\Http\Requests\ProductAttribute;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attribute_name' => ['sometimes', 'string', 'max:255'],
            'attribute_code' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('product_attributes', 'attribute_code')->ignore($this->route('product_attribute')),
            ],
            'attribute_type' => ['sometimes', 'string', 'in:text,select,multiselect,number,boolean'],
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'string'],
            'is_filterable' => ['sometimes', 'boolean'],
            'is_required' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
