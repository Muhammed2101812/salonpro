<?php

declare(strict_types=1);

namespace App\Http\Requests\ProductAttributeValue;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['sometimes', 'uuid', 'exists:products,id'],
            'attribute_id' => ['sometimes', 'uuid', 'exists:product_attributes,id'],
            'attribute_value' => ['sometimes', 'string'],
        ];
    }
}
