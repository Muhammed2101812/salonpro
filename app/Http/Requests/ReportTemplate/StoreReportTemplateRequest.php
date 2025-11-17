<?php

declare(strict_types=1);

namespace App\Http\Requests\ReportTemplate;

use Illuminate\Foundation\Http\FormRequest;

class StoreReportTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'template_name' => ['required', 'string', 'max:255'],
            'template_code' => ['required', 'string', 'max:100', 'unique:report_templates,template_code'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'parameters' => ['nullable', 'array'],
            'columns' => ['nullable', 'array'],
            'query' => ['nullable', 'string'],
            'output_format' => ['sometimes', 'string', 'in:pdf,excel,csv,json'],
            'template_file' => ['nullable', 'string'],
            'is_system' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
            'created_by' => ['sometimes', 'uuid', 'exists:users,id'],
        ];
    }
}
