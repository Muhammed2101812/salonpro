<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductBundleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'exists:branches,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
            'items' => ['required', 'array', 'min:2'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:1'],
            'items.*.discount_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'branch_id.required' => 'Şube seçimi zorunludur.',
            'branch_id.exists' => 'Seçilen şube geçersiz.',
            'name.required' => 'Paket adı zorunludur.',
            'name.max' => 'Paket adı en fazla 255 karakter olabilir.',
            'description.max' => 'Açıklama en fazla 1000 karakter olabilir.',
            'price.required' => 'Fiyat zorunludur.',
            'price.numeric' => 'Fiyat sayısal olmalıdır.',
            'price.min' => 'Fiyat negatif olamaz.',
            'is_active.boolean' => 'Durum geçerli bir boolean değer olmalıdır.',
            'start_date.date' => 'Geçerli bir başlangıç tarihi giriniz.',
            'end_date.date' => 'Geçerli bir bitiş tarihi giriniz.',
            'end_date.after_or_equal' => 'Bitiş tarihi, başlangıç tarihinden önce olamaz.',
            'items.required' => 'En az iki ürün eklemelisiniz.',
            'items.array' => 'Ürünler dizi formatında olmalıdır.',
            'items.min' => 'Paket için en az iki ürün eklemelisiniz.',
            'items.*.product_id.required' => 'Ürün seçimi zorunludur.',
            'items.*.product_id.exists' => 'Seçilen ürün geçersiz.',
            'items.*.quantity.required' => 'Miktar zorunludur.',
            'items.*.quantity.numeric' => 'Miktar sayısal olmalıdır.',
            'items.*.quantity.min' => 'Miktar en az 1 olmalıdır.',
            'items.*.discount_percentage.numeric' => 'İndirim oranı sayısal olmalıdır.',
            'items.*.discount_percentage.min' => 'İndirim oranı negatif olamaz.',
            'items.*.discount_percentage.max' => 'İndirim oranı %100\'ü geçemez.',
        ];
    }
}
