<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeShiftResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'employee_id' => $this->employee_id,
            'shift_date' => $this->shift_date?->format('Y-m-d'),
            'start_time' => $this->start_time,
            'end_time' => $this->end_time,
            'break_minutes' => $this->break_minutes,
            'status' => $this->status,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
            'employee' => $this->whenLoaded('employee', fn() => [
                'id' => $this->employee->id,
                'first_name' => $this->employee->first_name,
                'last_name' => $this->employee->last_name,
            ]),
            'branch' => $this->whenLoaded('branch', fn() => [
                'id' => $this->branch->id,
                'name' => $this->branch->name,
            ]),
        ];
    }
}
