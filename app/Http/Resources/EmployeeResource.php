<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'user_id' => $this->user_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'hire_date' => $this->hire_date?->format('Y-m-d'),
            'salary' => $this->when(
                $request->user()?->can('view-employee-salary'),
                $this->salary
            ),
            'specialties' => $this->specialties,
            'commission_rate' => (float) $this->commission_rate,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),

            // Relationships
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'user' => UserResource::make($this->whenLoaded('user')),

            // Computed fields
            'tenure_years' => $this->when(
                $this->hire_date,
                fn() => $this->hire_date->diffInYears(now())
            ),
            'tenure_display' => $this->when(
                $this->hire_date,
                fn() => $this->getTenureDisplay()
            ),
            'appointments_count' => $this->when(
                isset($this->appointments_count),
                $this->appointments_count
            ),
            'total_commissions' => $this->when(
                isset($this->total_commissions),
                $this->total_commissions
            ),
        ];
    }

    private function getTenureDisplay(): string
    {
        if (!$this->hire_date) {
            return 'N/A';
        }

        $years = $this->hire_date->diffInYears(now());
        $months = $this->hire_date->copy()->addYears($years)->diffInMonths(now());

        if ($years === 0) {
            return $months === 1 ? '1 month' : "{$months} months";
        }

        if ($months === 0) {
            return $years === 1 ? '1 year' : "{$years} years";
        }

        return "{$years}y {$months}m";
    }
}
