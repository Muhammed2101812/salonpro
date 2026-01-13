<?php

declare(strict_types=1);

namespace App\Http\Requests\ProductVariant;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        $variant = $this->route('variant');
        return $this->user()->can('update', $variant);
    }

    public function rules(): array
    {
        return [
            'variant_name' => ['sometimes', 'string', 'max:255'],
            'attributes' => ['sometimes', 'json'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'cost_price' => ['sometimes', 'numeric', 'min:0'],
            'reorder_level' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
