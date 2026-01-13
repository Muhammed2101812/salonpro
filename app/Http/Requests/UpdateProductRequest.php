<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->can('products.update') ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product');

        return [
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'barcode' => ['nullable', 'string', 'max:255', Rule::unique('products', 'barcode')->ignore($productId)],
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('products', 'sku')->ignore($productId)],
            'price' => ['sometimes', 'required', 'numeric', 'min:0', 'max:999999.99'],
            'cost_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'stock_quantity' => ['sometimes', 'required', 'integer', 'min:0'],
            'min_stock_quantity' => ['sometimes', 'required', 'integer', 'min:0'],
            'unit' => ['sometimes', 'required', 'string', 'max:50'],
            'category' => ['nullable', 'string', 'max:255'],
            'is_active' => ['boolean'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'ürün adı',
            'description' => 'açıklama',
            'barcode' => 'barkod',
            'sku' => 'stok kodu',
            'price' => 'satış fiyatı',
            'cost_price' => 'maliyet fiyatı',
            'stock_quantity' => 'stok miktarı',
            'min_stock_quantity' => 'minimum stok seviyesi',
            'unit' => 'birim',
            'category' => 'kategori',
            'is_active' => 'aktif durumu',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => ':attribute alanı zorunludur.',
            'barcode.unique' => 'Bu :attribute zaten kullanılıyor.',
            'sku.unique' => 'Bu :attribute zaten kullanılıyor.',
            'price.min' => ':attribute en az :min olmalıdır.',
            'price.max' => ':attribute en fazla :max olabilir.',
            'stock_quantity.min' => ':attribute en az :min olmalıdır.',
            'min_stock_quantity.min' => ':attribute en az :min olmalıdır.',
        ];
    }
}
