<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeValueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'attribute_id' => $this->attribute_id,
            'attribute_value' => $this->attribute_value,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships
            'product' => ProductResource::make($this->whenLoaded('product')),
            'attribute' => ProductAttributeResource::make($this->whenLoaded('attribute')),

            // Computed fields
            'attribute_name' => $this->when(
                $this->relationLoaded('attribute'),
                fn() => $this->attribute->attribute_name
            ),
            'attribute_code' => $this->when(
                $this->relationLoaded('attribute'),
                fn() => $this->attribute->attribute_code
            ),
            'attribute_type' => $this->when(
                $this->relationLoaded('attribute'),
                fn() => $this->attribute->attribute_type
            ),
            'formatted_value' => $this->getFormattedValue(),
        ];
    }

    private function getFormattedValue(): string
    {
        if (!$this->relationLoaded('attribute')) {
            return (string) $this->attribute_value;
        }

        return match($this->attribute->attribute_type) {
            'boolean' => $this->attribute_value ? 'Yes' : 'No',
            'number' => number_format((float) $this->attribute_value, 2),
            default => (string) $this->attribute_value,
        };
    }
}
