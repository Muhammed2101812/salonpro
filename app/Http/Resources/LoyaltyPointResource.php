<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LoyaltyPointResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'points' => (int) $this->points,
            'transaction_type' => $this->transaction_type,
            'reference_type' => $this->reference_type,
            'reference_id' => $this->reference_id,
            'description' => $this->description,
            'expires_at' => $this->expires_at?->format('Y-m-d H:i:s'),
            'expired_at' => $this->expired_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),

            // Relationships
            'customer' => CustomerResource::make($this->whenLoaded('customer')),

            // Computed fields
            'is_expired' => $this->when(
                $this->expires_at,
                fn() => $this->expires_at->isPast() || $this->expired_at !== null
            ),
            'days_until_expiry' => $this->when(
                $this->expires_at && !$this->expired_at,
                fn() => now()->diffInDays($this->expires_at, false)
            ),
            'points_display' => $this->transaction_type === 'earned' || $this->transaction_type === 'awarded'
                ? '+' . $this->points
                : $this->points,
        ];
    }
}
