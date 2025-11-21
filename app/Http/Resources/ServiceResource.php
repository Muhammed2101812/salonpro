<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'service_category_id' => $this->service_category_id,
            'name' => $this->name,
            'description' => $this->description,
            'price' => (float) $this->price,
            'cost' => (float) $this->cost,
            'duration_minutes' => (int) $this->duration_minutes,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deleted_at' => $this->deleted_at?->format('Y-m-d H:i:s'),

            // Relationships
            'service_category' => ServiceCategoryResource::make($this->whenLoaded('serviceCategory')),

            // Computed fields
            'duration_display' => $this->getDurationDisplay(),
            'profit_margin' => $this->when(
                $this->price > 0 && $this->cost > 0,
                fn() => round((($this->price - $this->cost) / $this->price) * 100, 2)
            ),
            'appointments_count' => $this->when(
                isset($this->appointments_count),
                $this->appointments_count
            ),
        ];
    }

    private function getDurationDisplay(): string
    {
        $hours = floor($this->duration_minutes / 60);
        $minutes = $this->duration_minutes % 60;

        if ($hours === 0) {
            return "{$minutes}min";
        }

        if ($minutes === 0) {
            return "{$hours}h";
        }

        return "{$hours}h {$minutes}min";
    }
}
