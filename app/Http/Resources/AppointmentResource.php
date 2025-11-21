<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'customer_id' => $this->customer_id,
            'employee_id' => $this->employee_id,
            'service_id' => $this->service_id,
            'appointment_date' => $this->appointment_date?->format('Y-m-d H:i:s'),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'duration_minutes' => (int) $this->duration_minutes,
            'price' => (float) $this->price,
            'status' => $this->status,
            'notes' => $this->notes,
            'cancellation_reason' => $this->cancellation_reason,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),

            // Relationships
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'employee' => EmployeeResource::make($this->whenLoaded('employee')),
            'service' => ServiceResource::make($this->whenLoaded('service')),

            // Computed fields
            'is_upcoming' => $this->when(
                $this->appointment_date,
                fn() => $this->appointment_date->isFuture()
            ),
            'is_today' => $this->when(
                $this->appointment_date,
                fn() => $this->appointment_date->isToday()
            ),
            'time_until' => $this->when(
                $this->appointment_date && $this->appointment_date->isFuture(),
                fn() => $this->appointment_date->diffForHumans()
            ),
            'status_badge' => $this->getStatusBadge(),
            'can_cancel' => in_array($this->status, ['scheduled', 'confirmed']),
            'can_complete' => $this->status === 'in_progress',
        ];
    }

    private function getStatusBadge(): array
    {
        return match($this->status) {
            'scheduled' => ['color' => 'info', 'label' => 'Scheduled'],
            'confirmed' => ['color' => 'primary', 'label' => 'Confirmed'],
            'in_progress' => ['color' => 'warning', 'label' => 'In Progress'],
            'completed' => ['color' => 'success', 'label' => 'Completed'],
            'cancelled' => ['color' => 'danger', 'label' => 'Cancelled'],
            'no_show' => ['color' => 'secondary', 'label' => 'No Show'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->status)],
        };
    }
}
