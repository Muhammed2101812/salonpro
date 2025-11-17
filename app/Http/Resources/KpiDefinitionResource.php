<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KpiDefinitionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'kpi_code' => $this->kpi_code,
            'kpi_name' => $this->kpi_name,
            'description' => $this->description,
            'category' => $this->category,
            'calculation_method' => $this->calculation_method,
            'calculation_formula' => $this->calculation_formula,
            'unit' => $this->unit,
            'frequency' => $this->frequency,
            'target_value' => (float) $this->target_value,
            'warning_threshold' => (float) $this->warning_threshold,
            'critical_threshold' => (float) $this->critical_threshold,
            'higher_is_better' => $this->higher_is_better,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships
            'values' => KpiValueResource::collection($this->whenLoaded('values')),

            // Computed fields
            'status_badge' => $this->getStatusBadge(),
            'category_badge' => $this->getCategoryBadge(),
            'frequency_badge' => $this->getFrequencyBadge(),
            'calculation_method_badge' => $this->getCalculationMethodBadge(),
            'has_thresholds' => !is_null($this->warning_threshold) || !is_null($this->critical_threshold),
            'has_target' => !is_null($this->target_value),
            'values_count' => $this->when(
                $this->relationLoaded('values'),
                fn() => $this->values->count()
            ),
            'can_activate' => !$this->is_active,
            'can_deactivate' => $this->is_active,
        ];
    }

    private function getStatusBadge(): array
    {
        return $this->is_active
            ? ['color' => 'success', 'label' => 'Active']
            : ['color' => 'secondary', 'label' => 'Inactive'];
    }

    private function getCategoryBadge(): array
    {
        return match(strtolower($this->category)) {
            'sales' => ['color' => 'success', 'label' => 'Sales', 'icon' => 'dollar'],
            'financial' => ['color' => 'info', 'label' => 'Financial', 'icon' => 'chart-line'],
            'customer' => ['color' => 'primary', 'label' => 'Customer', 'icon' => 'users'],
            'employee' => ['color' => 'purple', 'label' => 'Employee', 'icon' => 'user-tie'],
            'operational' => ['color' => 'warning', 'label' => 'Operational', 'icon' => 'cogs'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->category), 'icon' => 'chart-bar'],
        };
    }

    private function getFrequencyBadge(): array
    {
        return match($this->frequency) {
            'daily' => ['color' => 'primary', 'label' => 'Daily', 'icon' => 'calendar-day'],
            'weekly' => ['color' => 'info', 'label' => 'Weekly', 'icon' => 'calendar-week'],
            'monthly' => ['color' => 'success', 'label' => 'Monthly', 'icon' => 'calendar'],
            'quarterly' => ['color' => 'warning', 'label' => 'Quarterly', 'icon' => 'calendar-alt'],
            'yearly' => ['color' => 'danger', 'label' => 'Yearly', 'icon' => 'calendar-check'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->frequency), 'icon' => 'calendar'],
        };
    }

    private function getCalculationMethodBadge(): array
    {
        return match($this->calculation_method) {
            'sum' => ['color' => 'primary', 'label' => 'Sum', 'icon' => 'plus'],
            'average' => ['color' => 'info', 'label' => 'Average', 'icon' => 'equals'],
            'count' => ['color' => 'success', 'label' => 'Count', 'icon' => 'hashtag'],
            'percentage' => ['color' => 'warning', 'label' => 'Percentage', 'icon' => 'percent'],
            'ratio' => ['color' => 'purple', 'label' => 'Ratio', 'icon' => 'divide'],
            'formula' => ['color' => 'danger', 'label' => 'Formula', 'icon' => 'calculator'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->calculation_method), 'icon' => 'square'],
        };
    }
}
