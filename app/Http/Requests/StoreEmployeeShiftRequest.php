<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'branch_id' => ['required', 'uuid', 'exists:branches,id'],
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
            'shift_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'break_minutes' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'in:scheduled,confirmed,completed,cancelled'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
