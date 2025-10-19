<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInventoryMovementRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // TODO: Add authorization logic
    }

    public function rules(): array
    {
        $rules = [
            'product_id' => ['required', 'uuid', 'exists:products,id'],
            'type' => ['required', 'in:in,out,adjustment'],
            'quantity' => ['required', 'integer', 'min:1'],
            'reason' => ['nullable', 'string', 'max:1000'],
            'reference_type' => ['nullable', 'string', 'max:255'],
            'reference_id' => ['nullable', 'string', 'max:255'],
            'movement_date' => ['required', 'date', 'before_or_equal:today'],
        ];

        return $rules;
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Ürün seçilmelidir.',
            'product_id.exists' => 'Seçilen ürün bulunamadı.',
            'type.required' => 'Hareket tipi seçilmelidir.',
            'type.in' => 'Geçersiz hareket tipi.',
            'quantity.required' => 'Miktar girilmelidir.',
            'quantity.integer' => 'Miktar tam sayı olmalıdır.',
            'quantity.min' => 'Miktar en az 1 olmalıdır.',
            'movement_date.required' => 'Hareket tarihi girilmelidir.',
            'movement_date.date' => 'Geçerli bir tarih giriniz.',
            'movement_date.before_or_equal' => 'Hareket tarihi bugünden ileri olamaz.',
            'reason.max' => 'Açıklama en fazla 1000 karakter olabilir.',
        ];
    }

    public function attributes(): array
    {
        return [
            'product_id' => 'ürün',
            'type' => 'hareket tipi',
            'quantity' => 'miktar',
            'reason' => 'açıklama',
            'movement_date' => 'hareket tarihi',
        ];
    }
}
