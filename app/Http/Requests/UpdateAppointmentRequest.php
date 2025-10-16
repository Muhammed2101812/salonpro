<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentRequest extends FormRequest
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
            'branch_id' => ['sometimes', 'uuid', 'exists:branches,id'],
            'customer_id' => ['sometimes', 'uuid', 'exists:customers,id'],
            'employee_id' => ['sometimes', 'uuid', 'exists:employees,id'],
            'service_id' => ['sometimes', 'uuid', 'exists:services,id'],
            'appointment_date' => ['sometimes', 'date', 'after:now'],
            'duration_minutes' => ['sometimes', 'integer', 'min:1'],
            'price' => ['sometimes', 'numeric', 'min:0'],
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
            'branch_id.exists' => 'Seçilen :attribute geçerli değil.',
            'customer_id.exists' => 'Seçilen :attribute geçerli değil.',
            'employee_id.exists' => 'Seçilen :attribute geçerli değil.',
            'service_id.exists' => 'Seçilen :attribute geçerli değil.',
            'appointment_date.after' => ':attribute gelecek bir tarih olmalıdır.',
            'duration_minutes.min' => ':attribute en az :min dakika olmalıdır.',
            'price.min' => ':attribute en az :min olmalıdır.',
            'status.in' => 'Seçilen :attribute geçerli değil.',
            'notes.max' => ':attribute en fazla :max karakter olabilir.',
        ];
    }
}
