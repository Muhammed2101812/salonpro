<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_number' => $this->invoice_number,
            'customer_id' => $this->customer_id,
            'branch_id' => $this->branch_id,
            'issue_date' => $this->issue_date?->format('Y-m-d'),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'subtotal' => (float) $this->subtotal,
            'tax_amount' => (float) $this->tax_amount,
            'discount_amount' => (float) $this->discount_amount,
            'total' => (float) $this->total,
            'status' => $this->status,
            'notes' => $this->notes,
            'paid_at' => $this->paid_at?->format('Y-m-d H:i:s'),
            'cancelled_at' => $this->cancelled_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'items' => InvoiceItemResource::collection($this->whenLoaded('items')),
            'payments' => PaymentResource::collection($this->whenLoaded('payments')),

            // Computed fields
            'is_overdue' => $this->when(
                $this->status === 'pending' && $this->due_date,
                fn() => $this->due_date->isPast()
            ),
            'days_until_due' => $this->when(
                $this->status === 'pending' && $this->due_date,
                fn() => now()->diffInDays($this->due_date, false)
            ),
            'amount_paid' => $this->when(
                $this->relationLoaded('payments'),
                fn() => $this->payments->sum('amount')
            ),
        ];
    }
}
