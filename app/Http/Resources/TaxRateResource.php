<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaxRateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'rate' => (float) $this->rate,
            'description' => $this->description,
            'is_active' => (bool) $this->is_active,
            'effective_from' => $this->effective_from?->format('Y-m-d'),
            'effective_to' => $this->effective_to?->format('Y-m-d'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Computed fields
            'rate_display' => $this->rate . '%',
            'is_current' => $this->getIsCurrent(),
            'status' => $this->getStatus(),
        ];
    }

    private function getIsCurrent(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now()->toDateString();

        if ($this->effective_from && $now < $this->effective_from->toDateString()) {
            return false;
        }

        if ($this->effective_to && $now > $this->effective_to->toDateString()) {
            return false;
        }

        return true;
    }

    private function getStatus(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        $now = now()->toDateString();

        if ($this->effective_from && $now < $this->effective_from->toDateString()) {
            return 'upcoming';
        }

        if ($this->effective_to && $now > $this->effective_to->toDateString()) {
            return 'expired';
        }

        return 'active';
    }
}
