<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupplierResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'contact_person' => $this->contact_person,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'tax_number' => $this->tax_number,
            'payment_terms' => $this->payment_terms,
            'is_active' => (bool) $this->is_active,
            'notes' => $this->notes,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),

            // Computed fields
            'full_address' => $this->getFullAddress(),
            'status_badge' => $this->getStatusBadge(),

            // Relationships
            'purchase_orders' => PurchaseOrderResource::collection($this->whenLoaded('purchaseOrders')),

            // Aggregates
            'purchase_orders_count' => $this->when(
                isset($this->purchase_orders_count),
                $this->purchase_orders_count
            ),
            'total_purchases' => $this->when(
                isset($this->total_purchases),
                $this->total_purchases
            ),
        ];
    }

    private function getFullAddress(): string
    {
        $parts = array_filter([$this->address, $this->city, $this->country]);
        return implode(', ', $parts) ?: 'N/A';
    }

    private function getStatusBadge(): array
    {
        return $this->is_active
            ? ['color' => 'success', 'label' => 'Active']
            : ['color' => 'secondary', 'label' => 'Inactive'];
    }
}
