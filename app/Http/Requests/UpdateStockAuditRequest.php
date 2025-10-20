<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateStockAuditRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['sometimes', 'exists:branches,id'],
            'audit_date' => ['sometimes', 'date'],
            'status' => ['sometimes', 'in:pending,in_progress,completed,cancelled'],
            'notes' => ['nullable', 'string', 'max:1000'],
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.expected_quantity' => ['required', 'numeric', 'min:0'],
            'items.*.actual_quantity' => ['nullable', 'numeric', 'min:0'],
            'items.*.notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'branch_id.exists' => 'Seçilen şube geçersiz.',
            'audit_date.date' => 'Geçerli bir tarih giriniz.',
            'status.in' => 'Geçersiz durum değeri.',
            'notes.max' => 'Notlar en fazla 1000 karakter olabilir.',
            'items.array' => 'Ürünler dizi formatında olmalıdır.',
            'items.min' => 'En az bir ürün eklemelisiniz.',
            'items.*.product_id.required' => 'Ürün seçimi zorunludur.',
            'items.*.product_id.exists' => 'Seçilen ürün geçersiz.',
            'items.*.expected_quantity.required' => 'Beklenen miktar zorunludur.',
            'items.*.expected_quantity.numeric' => 'Beklenen miktar sayısal olmalıdır.',
            'items.*.expected_quantity.min' => 'Beklenen miktar negatif olamaz.',
            'items.*.actual_quantity.numeric' => 'Gerçek miktar sayısal olmalıdır.',
            'items.*.actual_quantity.min' => 'Gerçek miktar negatif olamaz.',
            'items.*.notes.max' => 'Ürün notu en fazla 500 karakter olabilir.',
        ];
    }
}
