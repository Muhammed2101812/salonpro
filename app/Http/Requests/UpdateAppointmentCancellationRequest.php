<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAppointmentCancellationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'appointment_id' => ['sometimes', 'uuid', 'exists:appointments,id'],
            'reason_id' => ['nullable', 'uuid', 'exists:appointment_cancellation_reasons,id'],
            'cancelled_by' => ['sometimes', 'uuid', 'exists:users,id'],
            'cancelled_at' => ['nullable', 'date'],
            'cancellation_notes' => ['nullable', 'string', 'max:1000'],
            'refund_issued' => ['nullable', 'boolean'],
            'refund_amount' => ['nullable', 'numeric', 'min:0', 'max:999999.99'],
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'appointment_id' => 'randevu',
            'reason_id' => 'iptal nedeni',
            'cancelled_by' => 'iptal eden',
            'cancelled_at' => 'iptal tarihi',
            'cancellation_notes' => 'iptal notu',
            'refund_issued' => 'iade durumu',
            'refund_amount' => 'iade tutarı',
        ];
    }
}
