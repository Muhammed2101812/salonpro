<?php

declare(strict_types=1);

namespace App\Http\Requests\EmployeeCommission;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeCommissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['sometimes', 'required', 'uuid', 'exists:employees,id'],
            'appointment_id' => ['nullable', 'uuid', 'exists:appointments,id'],
            'sale_id' => ['nullable', 'uuid', 'exists:sales,id'],
            'commission_type' => ['sometimes', 'required', 'string', 'in:service,product,sale'],
            'base_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'commission_rate' => ['sometimes', 'required', 'numeric', 'min:0', 'max:100'],
            'commission_amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'payment_status' => ['sometimes', 'string', 'in:unpaid,paid'],
            'paid_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
