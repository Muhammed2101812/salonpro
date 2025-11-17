<?php

declare(strict_types=1);

namespace App\Http\Requests\ReportTemplate;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateReportTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'template_name' => ['sometimes', 'string', 'max:255'],
            'template_code' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('report_templates', 'template_code')->ignore($this->route('report_template')),
            ],
            'description' => ['nullable', 'string'],
            'category' => ['sometimes', 'string', 'max:100'],
            'parameters' => ['nullable', 'array'],
            'columns' => ['nullable', 'array'],
            'query' => ['nullable', 'string'],
            'output_format' => ['sometimes', 'string', 'in:pdf,excel,csv,json'],
            'template_file' => ['nullable', 'string'],
            'is_system' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
