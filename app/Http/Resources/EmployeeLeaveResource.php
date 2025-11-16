<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeLeaveResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'leave_type' => $this->leave_type,
            'start_date' => $this->start_date?->format('Y-m-d'),
            'end_date' => $this->end_date?->format('Y-m-d'),
            'total_days' => (int) $this->total_days,
            'reason' => $this->reason,
            'status' => $this->status,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),

            // Relationships
            'employee' => EmployeeResource::make($this->whenLoaded('employee')),
            'approver' => UserResource::make($this->whenLoaded('approvedBy')),

            // Computed fields
            'status_badge' => $this->getStatusBadge(),
            'leave_type_badge' => $this->getLeaveTypeBadge(),
            'is_upcoming' => $this->when(
                $this->start_date,
                fn() => $this->start_date->isFuture()
            ),
            'is_current' => $this->when(
                $this->start_date && $this->end_date,
                fn() => now()->between($this->start_date, $this->end_date)
            ),
            'days_until_start' => $this->when(
                $this->start_date && $this->start_date->isFuture(),
                fn() => now()->diffInDays($this->start_date)
            ),
            'can_approve' => $this->status === 'pending',
            'can_reject' => $this->status === 'pending',
            'can_cancel' => in_array($this->status, ['pending', 'approved']),
        ];
    }

    private function getStatusBadge(): array
    {
        return match($this->status) {
            'pending' => ['color' => 'warning', 'label' => 'Pending'],
            'approved' => ['color' => 'success', 'label' => 'Approved'],
            'rejected' => ['color' => 'danger', 'label' => 'Rejected'],
            'cancelled' => ['color' => 'secondary', 'label' => 'Cancelled'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->status)],
        };
    }

    private function getLeaveTypeBadge(): array
    {
        return match($this->leave_type) {
            'annual' => ['color' => 'info', 'label' => 'Annual Leave', 'icon' => 'calendar'],
            'sick' => ['color' => 'warning', 'label' => 'Sick Leave', 'icon' => 'medical'],
            'personal' => ['color' => 'primary', 'label' => 'Personal Leave', 'icon' => 'user'],
            'maternity' => ['color' => 'pink', 'label' => 'Maternity Leave', 'icon' => 'baby'],
            'paternity' => ['color' => 'blue', 'label' => 'Paternity Leave', 'icon' => 'baby'],
            'unpaid' => ['color' => 'secondary', 'label' => 'Unpaid Leave', 'icon' => 'ban'],
            'other' => ['color' => 'secondary', 'label' => 'Other', 'icon' => 'question'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->leave_type), 'icon' => 'calendar'],
        };
    }
}
