<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReportTemplateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'template_name' => $this->template_name,
            'template_code' => $this->template_code,
            'description' => $this->description,
            'category' => $this->category,
            'parameters' => $this->parameters,
            'columns' => $this->columns,
            'query' => $this->query,
            'output_format' => $this->output_format,
            'template_file' => $this->template_file,
            'is_system' => $this->is_system,
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships
            'creator' => UserResource::make($this->whenLoaded('creator')),
            'schedules' => ReportScheduleResource::collection($this->whenLoaded('schedules')),
            'executions' => ReportExecutionResource::collection($this->whenLoaded('executions')),

            // Computed fields
            'status_badge' => $this->getStatusBadge(),
            'category_badge' => $this->getCategoryBadge(),
            'output_format_badge' => $this->getOutputFormatBadge(),
            'parameters_count' => is_array($this->parameters) ? count($this->parameters) : 0,
            'columns_count' => is_array($this->columns) ? count($this->columns) : 0,
            'executions_count' => $this->when(
                $this->relationLoaded('executions'),
                fn() => $this->executions->count()
            ),
            'can_edit' => !$this->is_system,
            'can_delete' => !$this->is_system,
            'can_activate' => !$this->is_active,
            'can_deactivate' => $this->is_active && !$this->is_system,
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
            'inventory' => ['color' => 'warning', 'label' => 'Inventory', 'icon' => 'box'],
            'customer' => ['color' => 'primary', 'label' => 'Customer', 'icon' => 'users'],
            'employee' => ['color' => 'purple', 'label' => 'Employee', 'icon' => 'user-tie'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->category), 'icon' => 'file'],
        };
    }

    private function getOutputFormatBadge(): array
    {
        return match($this->output_format) {
            'pdf' => ['color' => 'danger', 'label' => 'PDF', 'icon' => 'file-pdf'],
            'excel' => ['color' => 'success', 'label' => 'Excel', 'icon' => 'file-excel'],
            'csv' => ['color' => 'info', 'label' => 'CSV', 'icon' => 'file-csv'],
            'json' => ['color' => 'warning', 'label' => 'JSON', 'icon' => 'file-code'],
            default => ['color' => 'secondary', 'label' => strtoupper($this->output_format ?? 'N/A'), 'icon' => 'file'],
        };
    }
}
