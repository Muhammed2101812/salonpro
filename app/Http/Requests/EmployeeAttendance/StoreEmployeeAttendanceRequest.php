<?php

declare(strict_types=1);

namespace App\Http\Requests\EmployeeAttendance;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeAttendanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'uuid', 'exists:employees,id'],
            'branch_id' => ['required', 'uuid', 'exists:branches,id'],
            'status' => ['nullable', 'string', 'in:present,late,early_departure,absent'],
            'notes' => ['nullable', 'string'],
            'ip_address' => ['nullable', 'ip'],
            'location' => ['nullable', 'array'],
            'location.latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'location.longitude' => ['nullable', 'numeric', 'between:-180,180'],
        ];
    }
}
