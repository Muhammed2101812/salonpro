<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseOrderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'supplier_id' => $this->supplier_id,
            'order_number' => $this->order_number,
            'order_date' => $this->order_date?->format('Y-m-d'),
            'expected_delivery_date' => $this->expected_delivery_date?->format('Y-m-d'),
            'actual_delivery_date' => $this->actual_delivery_date?->format('Y-m-d'),
            'total_amount' => (float) $this->total_amount,
            'tax_amount' => (float) $this->tax_amount,
            'discount_amount' => (float) $this->discount_amount,
            'final_amount' => (float) $this->final_amount,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),

            // Relationships
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'supplier' => SupplierResource::make($this->whenLoaded('supplier')),
            'creator' => UserResource::make($this->whenLoaded('creator')),
            'items' => PurchaseOrderItemResource::collection($this->whenLoaded('items')),

            // Computed fields
            'status_badge' => $this->getStatusBadge(),
            'is_overdue' => $this->when(
                $this->expected_delivery_date && $this->status === 'pending',
                fn() => $this->expected_delivery_date->isPast()
            ),
            'days_until_delivery' => $this->when(
                $this->expected_delivery_date && $this->status === 'pending',
                fn() => now()->diffInDays($this->expected_delivery_date, false)
            ),
            'can_receive' => in_array($this->status, ['pending', 'approved']),
            'can_cancel' => in_array($this->status, ['pending', 'approved']),
        ];
    }

    private function getStatusBadge(): array
    {
        return match($this->status) {
            'pending' => ['color' => 'warning', 'label' => 'Pending'],
            'approved' => ['color' => 'info', 'label' => 'Approved'],
            'received' => ['color' => 'success', 'label' => 'Received'],
            'cancelled' => ['color' => 'danger', 'label' => 'Cancelled'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->status)],
        };
    }
}
