<?php

declare(strict_types=1);

namespace App\Http\Requests\EmployeeAttendance;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'clock_in' => ['sometimes', 'date'],
            'clock_out' => ['nullable', 'date', 'after:clock_in'],
            'break_start' => ['nullable', 'date', 'after:clock_in'],
            'break_end' => ['nullable', 'date', 'after:break_start'],
            'total_hours' => ['nullable', 'numeric', 'min:0', 'max:24'],
            'status' => ['sometimes', 'string', 'in:present,late,early_departure,absent'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
