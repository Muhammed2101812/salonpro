<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeCommissionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'appointment_id' => $this->appointment_id,
            'sale_id' => $this->sale_id,
            'commission_type' => $this->commission_type,
            'base_amount' => (float) $this->base_amount,
            'commission_rate' => (float) $this->commission_rate,
            'commission_amount' => (float) $this->commission_amount,
            'payment_status' => $this->payment_status,
            'paid_at' => $this->paid_at?->format('Y-m-d H:i:s'),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships
            'employee' => EmployeeResource::make($this->whenLoaded('employee')),
            'appointment' => AppointmentResource::make($this->whenLoaded('appointment')),
            'sale' => SaleResource::make($this->whenLoaded('sale')),

            // Computed fields
            'is_paid' => $this->payment_status === 'paid',
            'payment_status_badge' => $this->getPaymentStatusBadge(),
            'commission_type_badge' => $this->getCommissionTypeBadge(),
            'formatted_amount' => number_format($this->commission_amount, 2),
            'days_since_earned' => $this->when(
                $this->created_at,
                fn() => $this->created_at->diffInDays(now())
            ),
        ];
    }

    private function getPaymentStatusBadge(): array
    {
        return match($this->payment_status) {
            'paid' => ['color' => 'success', 'label' => 'Paid'],
            'unpaid' => ['color' => 'warning', 'label' => 'Unpaid'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->payment_status)],
        };
    }

    private function getCommissionTypeBadge(): array
    {
        return match($this->commission_type) {
            'service' => ['color' => 'primary', 'label' => 'Service', 'icon' => 'scissors'],
            'product' => ['color' => 'info', 'label' => 'Product', 'icon' => 'shopping-bag'],
            'sale' => ['color' => 'success', 'label' => 'Sale', 'icon' => 'dollar'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->commission_type), 'icon' => 'star'],
        };
    }
}
