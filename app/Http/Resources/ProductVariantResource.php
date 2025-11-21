<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductVariantResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'sku' => $this->sku,
            'barcode' => $this->barcode,
            'variant_name' => $this->variant_name,
            'attributes' => $this->attributes,
            'price' => (float) $this->price,
            'cost_price' => (float) $this->cost_price,
            'stock_quantity' => (int) $this->stock_quantity,
            'reorder_level' => (int) $this->reorder_level,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships
            'product' => ProductResource::make($this->whenLoaded('product')),

            // Computed fields
            'profit_margin' => $this->when(
                $this->cost_price > 0,
                fn() => round((($this->price - $this->cost_price) / $this->price) * 100, 2)
            ),
            'profit_amount' => (float) ($this->price - $this->cost_price),
            'is_low_stock' => $this->stock_quantity <= $this->reorder_level,
            'stock_status' => $this->getStockStatus(),
            'stock_value' => (float) ($this->stock_quantity * $this->cost_price),
        ];
    }

    private function getStockStatus(): string
    {
        if ($this->stock_quantity <= 0) {
            return 'out_of_stock';
        }

        if ($this->stock_quantity <= $this->reorder_level) {
            return 'low_stock';
        }

        if ($this->stock_quantity <= ($this->reorder_level * 2)) {
            return 'medium_stock';
        }

        return 'in_stock';
    }
}
