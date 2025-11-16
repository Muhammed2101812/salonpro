<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'description' => $this->description,
            'barcode' => $this->barcode,
            'sku' => $this->sku,
            'price' => (float) $this->price,
            'cost_price' => (float) $this->cost_price,
            'stock_quantity' => (int) $this->stock_quantity,
            'min_stock_quantity' => (int) $this->min_stock_quantity,
            'unit' => $this->unit,
            'category' => $this->category,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),

            // Relationships
            'variants' => ProductVariantResource::collection($this->whenLoaded('variants')),

            // Computed fields
            'profit_margin' => $this->when(
                $this->price > 0 && $this->cost_price > 0,
                fn() => round((($this->price - $this->cost_price) / $this->price) * 100, 2)
            ),
            'stock_value' => round($this->stock_quantity * $this->cost_price, 2),
            'is_low_stock' => $this->stock_quantity > 0 && $this->stock_quantity <= $this->min_stock_quantity,
            'is_out_of_stock' => $this->stock_quantity <= 0,
            'stock_status' => $this->getStockStatus(),
            'stock_badge' => $this->getStockBadge(),
        ];
    }

    private function getStockStatus(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        }

        if ($this->stock_quantity <= $this->min_stock_quantity) {
            return 'low_stock';
        }

        if ($this->stock_quantity <= ($this->min_stock_quantity * 2)) {
            return 'medium_stock';
        }

        return 'in_stock';
    }

    private function getStockBadge(): array
    {
        return match($this->getStockStatus()) {
            'out_of_stock' => ['color' => 'danger', 'label' => 'Out of Stock'],
            'low_stock' => ['color' => 'warning', 'label' => 'Low Stock'],
            'medium_stock' => ['color' => 'info', 'label' => 'Medium Stock'],
            'in_stock' => ['color' => 'success', 'label' => 'In Stock'],
            default => ['color' => 'secondary', 'label' => 'Unknown'],
        };
    }
}
