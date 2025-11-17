<?php

declare(strict_types=1);

namespace App\Http\Requests\KpiDefinition;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateKpiDefinitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kpi_code' => [
                'sometimes',
                'string',
                'max:100',
                Rule::unique('kpi_definitions', 'kpi_code')->ignore($this->route('kpi_definition')),
            ],
            'kpi_name' => ['sometimes', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['sometimes', 'string', 'max:100'],
            'calculation_method' => ['sometimes', 'string', 'in:sum,average,count,percentage,ratio,formula'],
            'calculation_formula' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:50'],
            'frequency' => ['sometimes', 'string', 'in:daily,weekly,monthly,quarterly,yearly'],
            'target_value' => ['nullable', 'numeric'],
            'warning_threshold' => ['nullable', 'numeric'],
            'critical_threshold' => ['nullable', 'numeric'],
            'higher_is_better' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
