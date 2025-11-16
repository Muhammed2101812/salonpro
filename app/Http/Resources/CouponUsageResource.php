<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponUsageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'coupon_id' => $this->coupon_id,
            'customer_id' => $this->customer_id,
            'appointment_id' => $this->appointment_id,
            'sale_id' => $this->sale_id,
            'discount_amount' => (float) $this->discount_amount,
            'used_at' => $this->used_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),

            // Relationships
            'coupon' => CouponResource::make($this->whenLoaded('coupon')),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'appointment' => AppointmentResource::make($this->whenLoaded('appointment')),
            'sale' => SaleResource::make($this->whenLoaded('sale')),

            // Computed fields
            'used_for' => $this->getUsageContext(),
            'time_ago' => $this->when(
                $this->used_at,
                fn() => $this->used_at->diffForHumans()
            ),
        ];
    }

    private function getUsageContext(): string
    {
        if ($this->appointment_id) {
            return 'appointment';
        }

        if ($this->sale_id) {
            return 'sale';
        }

        return 'other';
    }
}
