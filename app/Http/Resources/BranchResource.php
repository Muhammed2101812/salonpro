<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BranchResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),

            // Computed fields
            'full_address' => $this->getFullAddress(),
            'status_badge' => $this->getStatusBadge(),

            // Relationships
            'employees_count' => $this->when(isset($this->employees_count), $this->employees_count),
            'customers_count' => $this->when(isset($this->customers_count), $this->customers_count),
        ];
    }

    private function getFullAddress(): string
    {
        $parts = array_filter([$this->address, $this->city, $this->country]);
        return implode(', ', $parts);
    }

    private function getStatusBadge(): array
    {
        return $this->is_active
            ? ['color' => 'success', 'label' => 'Active']
            : ['color' => 'secondary', 'label' => 'Inactive'];
    }
}
