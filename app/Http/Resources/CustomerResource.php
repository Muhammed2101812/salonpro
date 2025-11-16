<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'phone' => $this->phone,
            'email' => $this->email,
            'date_of_birth' => $this->date_of_birth?->format('Y-m-d'),
            'gender' => $this->gender,
            'address' => $this->address,
            'notes' => $this->notes,
            'is_active' => (bool) $this->is_active,
            'is_vip' => (bool) $this->is_vip,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),

            // Relationships
            'branch' => BranchResource::make($this->whenLoaded('branch')),

            // Computed fields
            'age' => $this->when(
                $this->date_of_birth,
                fn() => $this->date_of_birth->age
            ),
            'vip_badge' => $this->when(
                $this->is_vip,
                fn() => ['color' => 'gold', 'label' => 'VIP', 'icon' => 'star']
            ),
            'loyalty_points_balance' => $this->when(
                isset($this->loyalty_points_balance),
                $this->loyalty_points_balance
            ),
            'appointments_count' => $this->when(
                isset($this->appointments_count),
                $this->appointments_count
            ),
        ];
    }
}
