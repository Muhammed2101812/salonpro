<?php

declare(strict_types=1);

namespace App\Http\Requests\KpiDefinition;

use Illuminate\Foundation\Http\FormRequest;

class StoreKpiDefinitionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kpi_code' => ['required', 'string', 'max:100', 'unique:kpi_definitions,kpi_code'],
            'kpi_name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'calculation_method' => ['required', 'string', 'in:sum,average,count,percentage,ratio,formula'],
            'calculation_formula' => ['nullable', 'string'],
            'unit' => ['nullable', 'string', 'max:50'],
            'frequency' => ['required', 'string', 'in:daily,weekly,monthly,quarterly,yearly'],
            'target_value' => ['nullable', 'numeric'],
            'warning_threshold' => ['nullable', 'numeric'],
            'critical_threshold' => ['nullable', 'numeric'],
            'higher_is_better' => ['sometimes', 'boolean'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }
}
