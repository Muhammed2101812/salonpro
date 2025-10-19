<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'appointment_id' => 'nullable|uuid|exists:appointments,id',
            'sale_id' => 'nullable|uuid|exists:sales,id',
            'customer_id' => 'sometimes|required|uuid|exists:customers,id',
            'amount' => 'sometimes|required|numeric|min:0',
            'payment_method' => 'sometimes|required|in:cash,credit_card,debit_card,bank_transfer',
            'payment_date' => 'sometimes|required|date',
            'status' => 'nullable|in:pending,completed,failed,refunded',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'appointment_id.uuid' => 'Geçersiz randevu ID formatı.',
            'appointment_id.exists' => 'Seçilen randevu bulunamadı.',
            'sale_id.uuid' => 'Geçersiz satış ID formatı.',
            'sale_id.exists' => 'Seçilen satış bulunamadı.',
            'customer_id.required' => 'Müşteri seçilmelidir.',
            'customer_id.uuid' => 'Geçersiz müşteri ID formatı.',
            'customer_id.exists' => 'Seçilen müşteri bulunamadı.',
            'amount.required' => 'Ödeme tutarı girilmelidir.',
            'amount.numeric' => 'Ödeme tutarı sayı olmalıdır.',
            'amount.min' => 'Ödeme tutarı 0\'dan küçük olamaz.',
            'payment_method.required' => 'Ödeme yöntemi seçilmelidir.',
            'payment_method.in' => 'Geçersiz ödeme yöntemi.',
            'payment_date.required' => 'Ödeme tarihi girilmelidir.',
            'payment_date.date' => 'Geçerli bir tarih giriniz.',
            'status.in' => 'Geçersiz ödeme durumu.',
            'notes.max' => 'Notlar en fazla 1000 karakter olabilir.',
        ];
    }
}
