<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CashRegisterResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'name' => $this->name,
            'description' => $this->description,
            'opening_balance' => (float) $this->opening_balance,
            'current_balance' => (float) $this->current_balance,
            'is_active' => (bool) $this->is_active,
            'location' => $this->location,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),

            // Relationships
            'branch' => BranchResource::make($this->whenLoaded('branch')),

            // Computed fields
            'status_badge' => $this->getStatusBadge(),
            'variance' => $this->current_balance - $this->opening_balance,
            'variance_percentage' => $this->getVariancePercentage(),
            'balance_status' => $this->getBalanceStatus(),
        ];
    }

    private function getStatusBadge(): array
    {
        return $this->is_active
            ? ['color' => 'success', 'label' => 'Open']
            : ['color' => 'secondary', 'label' => 'Closed'];
    }

    private function getVariancePercentage(): ?float
    {
        if ($this->opening_balance == 0) {
            return null;
        }

        $variance = $this->current_balance - $this->opening_balance;
        return round(($variance / $this->opening_balance) * 100, 2);
    }

    private function getBalanceStatus(): string
    {
        if ($this->current_balance < 0) {
            return 'negative';
        }

        if ($this->current_balance === 0.0 || $this->current_balance === 0) {
            return 'zero';
        }

        return 'positive';
    }
}
