<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockAlertResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'product_id' => $this->product_id,
            'alert_type' => $this->alert_type,
            'threshold_quantity' => (float) $this->threshold_quantity,
            'current_quantity' => (float) $this->current_quantity,
            'priority' => $this->priority,
            'status' => $this->status,
            'notified_at' => $this->notified_at?->format('Y-m-d H:i:s'),
            'resolved_at' => $this->resolved_at?->format('Y-m-d H:i:s'),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'product' => ProductResource::make($this->whenLoaded('product')),

            // Computed fields
            'is_active' => $this->status === 'active',
            'is_resolved' => $this->status === 'resolved',
            'is_notified' => !is_null($this->notified_at),
            'alert_type_badge' => $this->getAlertTypeBadge(),
            'status_badge' => $this->getStatusBadge(),
            'priority_badge' => $this->getPriorityBadge(),
            'quantity_diff' => (float) ($this->threshold_quantity - $this->current_quantity),
            'quantity_diff_percentage' => $this->getQuantityDiffPercentage(),
            'days_unresolved' => $this->when(
                $this->status === 'active' && $this->created_at,
                fn() => $this->created_at->diffInDays(now())
            ),
            'can_resolve' => $this->status === 'active',
            'can_notify' => $this->status === 'active' && is_null($this->notified_at),
        ];
    }

    private function getAlertTypeBadge(): array
    {
        return match($this->alert_type) {
            'low_stock' => ['color' => 'warning', 'label' => 'Low Stock', 'icon' => 'arrow-down'],
            'out_of_stock' => ['color' => 'danger', 'label' => 'Out of Stock', 'icon' => 'x-circle'],
            'overstock' => ['color' => 'info', 'label' => 'Overstock', 'icon' => 'arrow-up'],
            'expiring_soon' => ['color' => 'orange', 'label' => 'Expiring Soon', 'icon' => 'clock'],
            default => ['color' => 'secondary', 'label' => ucfirst(str_replace('_', ' ', $this->alert_type)), 'icon' => 'alert'],
        };
    }

    private function getStatusBadge(): array
    {
        return match($this->status) {
            'active' => ['color' => 'danger', 'label' => 'Active'],
            'resolved' => ['color' => 'success', 'label' => 'Resolved'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->status)],
        };
    }

    private function getPriorityBadge(): array
    {
        return match($this->priority) {
            5 => ['color' => 'danger', 'label' => 'Critical', 'level' => 'high'],
            4 => ['color' => 'warning', 'label' => 'High', 'level' => 'high'],
            3 => ['color' => 'info', 'label' => 'Medium', 'level' => 'medium'],
            2 => ['color' => 'primary', 'label' => 'Low', 'level' => 'low'],
            1 => ['color' => 'secondary', 'label' => 'Very Low', 'level' => 'low'],
            default => ['color' => 'secondary', 'label' => 'Unknown', 'level' => 'low'],
        };
    }

    private function getQuantityDiffPercentage(): ?float
    {
        if ($this->threshold_quantity == 0) {
            return null;
        }

        $diff = $this->threshold_quantity - $this->current_quantity;
        return round(($diff / $this->threshold_quantity) * 100, 2);
    }
}
