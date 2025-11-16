<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'description' => $this->description,
            'discount_type' => $this->discount_type,
            'discount_value' => (float) $this->discount_value,
            'max_discount_amount' => $this->max_discount_amount ? (float) $this->max_discount_amount : null,
            'minimum_purchase' => $this->minimum_purchase ? (float) $this->minimum_purchase : null,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'usage_limit' => $this->usage_limit,
            'usage_limit_per_customer' => $this->usage_limit_per_customer,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships
            'usages' => CouponUsageResource::collection($this->whenLoaded('usages')),

            // Computed fields
            'discount_display' => $this->discount_type === 'percentage'
                ? $this->discount_value . '%'
                : '₺' . number_format($this->discount_value, 2),
            'is_valid' => $this->getIsValid(),
            'usage_count' => $this->when(
                $this->relationLoaded('usages'),
                fn() => $this->usages->count()
            ),
            'remaining_uses' => $this->when(
                $this->usage_limit && $this->relationLoaded('usages'),
                fn() => max(0, $this->usage_limit - $this->usages->count())
            ),
            'validity_status' => $this->getValidityStatus(),
        ];
    }

    private function getIsValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    private function getValidityStatus(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        $now = now();

        if ($this->start_date && $now->lt($this->start_date)) {
            return 'upcoming';
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return 'expired';
        }

        if ($this->usage_limit && $this->relationLoaded('usages')) {
            if ($this->usages->count() >= $this->usage_limit) {
                return 'limit_reached';
            }
        }

        return 'active';
    }
}
