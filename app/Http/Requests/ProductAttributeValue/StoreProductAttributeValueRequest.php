<?php

declare(strict_types=1);

namespace App\Http\Requests\ProductAttributeValue;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductAttributeValueRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'uuid', 'exists:products,id'],
            'attribute_id' => ['required', 'uuid', 'exists:product_attributes,id'],
            'attribute_value' => ['required', 'string'],
        ];
    }
}
