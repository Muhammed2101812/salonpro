<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductAttributeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'attribute_name' => $this->attribute_name,
            'attribute_code' => $this->attribute_code,
            'attribute_type' => $this->attribute_type,
            'options' => $this->options,
            'is_filterable' => $this->is_filterable,
            'is_required' => $this->is_required,
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships
            'values' => ProductAttributeValueResource::collection($this->whenLoaded('values')),

            // Computed fields
            'type_badge' => $this->getTypeBadge(),
            'has_options' => !empty($this->options),
            'options_count' => is_array($this->options) ? count($this->options) : 0,
            'values_count' => $this->when(
                $this->relationLoaded('values'),
                fn() => $this->values->count()
            ),
        ];
    }

    private function getTypeBadge(): array
    {
        return match($this->attribute_type) {
            'text' => ['color' => 'primary', 'label' => 'Text', 'icon' => 'text'],
            'select' => ['color' => 'info', 'label' => 'Select', 'icon' => 'list'],
            'multiselect' => ['color' => 'success', 'label' => 'Multi-Select', 'icon' => 'check-square'],
            'number' => ['color' => 'warning', 'label' => 'Number', 'icon' => 'hash'],
            'boolean' => ['color' => 'secondary', 'label' => 'Boolean', 'icon' => 'toggle-on'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->attribute_type), 'icon' => 'square'],
        };
    }
}
