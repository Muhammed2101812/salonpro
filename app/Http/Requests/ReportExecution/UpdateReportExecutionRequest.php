<?php

declare(strict_types=1);

namespace App\Http\Requests\ReportExecution;

use Illuminate\Foundation\Http\FormRequest;

class UpdateReportExecutionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', 'in:pending,completed,failed'],
            'parameters' => ['nullable', 'array'],
            'output_file' => ['nullable', 'string'],
            'output_format' => ['nullable', 'string', 'in:pdf,excel,csv,json'],
            'row_count' => ['nullable', 'integer', 'min:0'],
            'file_size' => ['nullable', 'integer', 'min:0'],
            'error_message' => ['nullable', 'string'],
        ];
    }
}
