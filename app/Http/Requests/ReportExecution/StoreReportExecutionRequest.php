<?php

declare(strict_types=1);

namespace App\Http\Requests\ReportExecution;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportExecutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'template_id' => ['required', 'uuid', 'exists:report_templates,id'],
            'schedule_id' => ['nullable', 'uuid', 'exists:report_schedules,id'],
            'branch_id' => ['nullable', 'uuid', 'exists:branches,id'],
            'parameters' => ['nullable', 'array'],
            'executed_by' => ['sometimes', 'uuid', 'exists:users,id'],
        ];
    }
}
