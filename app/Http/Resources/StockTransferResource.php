<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StockTransferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'from_branch_id' => $this->from_branch_id,
            'to_branch_id' => $this->to_branch_id,
            'product_variant_id' => $this->product_variant_id,
            'quantity' => (int) $this->quantity,
            'status' => $this->status,
            'transfer_date' => $this->transfer_date?->format('Y-m-d H:i:s'),
            'received_date' => $this->received_date?->format('Y-m-d H:i:s'),
            'notes' => $this->notes,
            'rejection_reason' => $this->when(
                $this->status === 'rejected',
                $this->rejection_reason
            ),
            'cancellation_reason' => $this->when(
                $this->status === 'cancelled',
                $this->cancellation_reason
            ),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),
            'rejected_at' => $this->rejected_at?->format('Y-m-d H:i:s'),
            'cancelled_at' => $this->cancelled_at?->format('Y-m-d H:i:s'),

            // Relationships
            'from_branch' => BranchResource::make($this->whenLoaded('fromBranch')),
            'to_branch' => BranchResource::make($this->whenLoaded('toBranch')),
            'product_variant' => ProductVariantResource::make($this->whenLoaded('productVariant')),
            'created_by' => UserResource::make($this->whenLoaded('createdBy')),
            'approved_by' => UserResource::make($this->whenLoaded('approvedBy')),
            'received_by' => UserResource::make($this->whenLoaded('receivedBy')),

            // Computed fields
            'status_badge' => $this->getStatusBadge(),
            'in_transit_days' => $this->when(
                $this->status === 'in_transit' && $this->transfer_date,
                fn() => now()->diffInDays($this->transfer_date)
            ),
            'can_approve' => $this->status === 'pending',
            'can_complete' => $this->status === 'in_transit',
            'can_cancel' => in_array($this->status, ['pending', 'in_transit']),
        ];
    }

    private function getStatusBadge(): array
    {
        return match($this->status) {
            'pending' => ['color' => 'warning', 'label' => 'Pending Approval'],
            'in_transit' => ['color' => 'info', 'label' => 'In Transit'],
            'completed' => ['color' => 'success', 'label' => 'Completed'],
            'rejected' => ['color' => 'danger', 'label' => 'Rejected'],
            'cancelled' => ['color' => 'secondary', 'label' => 'Cancelled'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->status)],
        };
    }
}
