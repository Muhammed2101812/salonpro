<?php

declare(strict_types=1);

namespace App\Http\Requests\ProductAttribute;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductAttributeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'attribute_name' => ['required', 'string', 'max:255'],
            'attribute_code' => ['required', 'string', 'max:100', 'unique:product_attributes,attribute_code'],
            'attribute_type' => ['required', 'string', 'in:text,select,multiselect,number,boolean'],
            'options' => ['nullable', 'array'],
            'options.*' => ['nullable', 'string'],
            'is_filterable' => ['sometimes', 'boolean'],
            'is_required' => ['sometimes', 'boolean'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
        ];
    }
}
