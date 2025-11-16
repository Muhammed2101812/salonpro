<?php

declare(strict_types=1);

namespace App\Http\Requests\EmployeeLeave;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['sometimes', 'required', 'uuid', 'exists:employees,id'],
            'leave_type' => ['sometimes', 'required', 'string', 'in:annual,sick,personal,maternity,paternity,unpaid,other'],
            'start_date' => ['sometimes', 'required', 'date'],
            'end_date' => ['sometimes', 'required', 'date', 'after_or_equal:start_date'],
            'reason' => ['sometimes', 'required', 'string', 'max:1000'],
            'status' => ['sometimes', 'string', 'in:pending,approved,rejected,cancelled'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
