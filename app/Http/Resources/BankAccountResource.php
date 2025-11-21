<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BankAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'bank_name' => $this->bank_name,
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'iban' => $this->iban,
            'swift_code' => $this->swift_code,
            'currency' => $this->currency,
            'current_balance' => (float) $this->current_balance,
            'is_active' => (bool) $this->is_active,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),

            // Relationships
            'branch' => BranchResource::make($this->whenLoaded('branch')),

            // Computed fields
            'status_badge' => $this->getStatusBadge(),
            'formatted_balance' => $this->getFormattedBalance(),
            'balance_status' => $this->getBalanceStatus(),
        ];
    }

    private function getStatusBadge(): array
    {
        return $this->is_active
            ? ['color' => 'success', 'label' => 'Active']
            : ['color' => 'secondary', 'label' => 'Inactive'];
    }

    private function getFormattedBalance(): string
    {
        return $this->currency . ' ' . number_format($this->current_balance, 2);
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
