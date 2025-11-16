<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeAttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'branch_id' => $this->branch_id,
            'clock_in' => $this->clock_in?->format('Y-m-d H:i:s'),
            'clock_out' => $this->clock_out?->format('Y-m-d H:i:s'),
            'break_start' => $this->break_start?->format('Y-m-d H:i:s'),
            'break_end' => $this->break_end?->format('Y-m-d H:i:s'),
            'total_hours' => (float) $this->total_hours,
            'status' => $this->status,
            'notes' => $this->notes,
            'ip_address' => $this->ip_address,
            'location' => $this->location,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships
            'employee' => EmployeeResource::make($this->whenLoaded('employee')),
            'branch' => BranchResource::make($this->whenLoaded('branch')),

            // Computed fields
            'is_clocked_in' => !$this->clock_out,
            'is_on_break' => $this->break_start && !$this->break_end,
            'status_badge' => $this->getStatusBadge(),
            'work_duration' => $this->when(
                $this->clock_in && $this->clock_out,
                fn() => $this->getWorkDuration()
            ),
            'break_duration' => $this->when(
                $this->break_start && $this->break_end,
                fn() => $this->getBreakDuration()
            ),
            'shift_date' => $this->clock_in?->format('Y-m-d'),
        ];
    }

    private function getStatusBadge(): array
    {
        return match($this->status) {
            'present' => ['color' => 'success', 'label' => 'Present'],
            'late' => ['color' => 'warning', 'label' => 'Late'],
            'early_departure' => ['color' => 'info', 'label' => 'Early Departure'],
            'absent' => ['color' => 'danger', 'label' => 'Absent'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->status)],
        };
    }

    private function getWorkDuration(): string
    {
        if (!$this->clock_in || !$this->clock_out) {
            return 'N/A';
        }

        $minutes = $this->clock_out->diffInMinutes($this->clock_in);
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return "{$hours}h {$mins}m";
    }

    private function getBreakDuration(): string
    {
        if (!$this->break_start || !$this->break_end) {
            return 'N/A';
        }

        $minutes = $this->break_end->diffInMinutes($this->break_start);
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return "{$hours}h {$mins}m";
    }
}
