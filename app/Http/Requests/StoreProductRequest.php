<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // TODO: Add authorization logic based on roles/permissions
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'barcode' => ['nullable', 'string', 'max:255', 'unique:products,barcode'],
            'sku' => ['nullable', 'string', 'max:255', 'unique:products,sku'],
            'price' => ['required', 'numeric', 'min:0', 'max:999999.99'],
            'cost_price' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
            'stock_quantity' => ['required', 'integer', 'min:0'],
            'min_stock_quantity' => ['required', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
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
