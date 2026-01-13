<?php

declare(strict_types=1);

namespace App\Http\Requests\ProductVariant;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductVariantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', \App\Models\ProductVariant::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'uuid', 'exists:products,id'],
            'sku' => ['nullable', 'string', 'max:100', 'unique:product_variants,sku'],
            'barcode' => ['nullable', 'string', 'max:100', 'unique:product_variants,barcode'],
            'variant_name' => ['required', 'string', 'max:255'],
            'attributes' => ['nullable', 'json'],
            'price' => ['required', 'numeric', 'min:0'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'stock_quantity' => ['nullable', 'integer', 'min:0'],
            'reorder_level' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product',
            'product_id.exists' => 'Selected product does not exist',
            'sku.unique' => 'This SKU already exists',
            'barcode.unique' => 'This barcode already exists',
            'variant_name.required' => 'Variant name is required',
            'price.required' => 'Price is required',
            'price.min' => 'Price must be greater than or equal to 0',
        ];
    }
}
