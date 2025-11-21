<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['sometimes', 'uuid', 'exists:branches,id'],
            'employee_id' => ['sometimes', 'uuid', 'exists:employees,id'],
            'shift_date' => ['sometimes', 'date'],
            'start_time' => ['sometimes', 'date_format:H:i'],
            'end_time' => ['sometimes', 'date_format:H:i', 'after:start_time'],
            'break_minutes' => ['nullable', 'integer', 'min:0'],
            'status' => ['sometimes', 'in:scheduled,confirmed,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
