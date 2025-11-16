<?php

declare(strict_types=1);

namespace App\Http\Requests\EmployeeLeave;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
            'leave_type' => ['required', 'string', 'in:annual,sick,personal,maternity,paternity,unpaid,other'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
