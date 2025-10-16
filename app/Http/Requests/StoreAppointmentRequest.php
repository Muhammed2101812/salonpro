<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAppointmentRequest extends FormRequest
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
            'branch_id' => ['required', 'uuid', 'exists:branches,id'],
            'customer_id' => ['required', 'uuid', 'exists:customers,id'],
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
            'service_id' => ['required', 'uuid', 'exists:services,id'],
            'appointment_date' => ['required', 'date', 'after:now'],
            'duration_minutes' => ['required', 'integer', 'min:1'],
            'price' => ['required', 'numeric', 'min:0'],
            'status' => ['in:pending,confirmed,cancelled,completed'],
            'notes' => ['nullable', 'string', 'max:1000'],
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
            'branch_id' => 'şube',
            'customer_id' => 'müşteri',
            'employee_id' => 'çalışan',
            'service_id' => 'hizmet',
            'appointment_date' => 'randevu tarihi',
            'duration_minutes' => 'süre',
            'price' => 'fiyat',
            'status' => 'durum',
            'notes' => 'notlar',
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
            'branch_id.required' => ':attribute alanı zorunludur.',
            'branch_id.exists' => 'Seçilen :attribute geçerli değil.',
            'customer_id.required' => ':attribute alanı zorunludur.',
            'customer_id.exists' => 'Seçilen :attribute geçerli değil.',
            'employee_id.required' => ':attribute alanı zorunludur.',
            'employee_id.exists' => 'Seçilen :attribute geçerli değil.',
            'service_id.required' => ':attribute alanı zorunludur.',
            'service_id.exists' => 'Seçilen :attribute geçerli değil.',
            'appointment_date.required' => ':attribute alanı zorunludur.',
            'appointment_date.after' => ':attribute gelecek bir tarih olmalıdır.',
            'duration_minutes.required' => ':attribute alanı zorunludur.',
            'duration_minutes.min' => ':attribute en az :min dakika olmalıdır.',
            'price.required' => ':attribute alanı zorunludur.',
            'price.min' => ':attribute en az :min olmalıdır.',
            'status.in' => 'Seçilen :attribute geçerli değil.',
            'notes.max' => ':attribute en fazla :max karakter olabilir.',
        ];
    }
}
