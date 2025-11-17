<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebhookResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'name' => $this->name,
            'url' => $this->url,
            'events' => $this->events,
            'secret' => $this->when($request->user()?->hasRole(['super_admin', 'admin']), $this->secret),
            'is_active' => $this->is_active,
            'timeout' => $this->timeout,
            'max_retries' => $this->max_retries,
            'headers' => $this->headers,
            'success_count' => $this->success_count,
            'failure_count' => $this->failure_count,
            'last_triggered_at' => $this->last_triggered_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),

            // Relationships
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'creator' => UserResource::make($this->whenLoaded('creator')),

            // Computed fields
            'total_attempts' => $this->success_count + $this->failure_count,
            'success_rate' => $this->calculateSuccessRate(),
            'health_status' => $this->getHealthStatus(),
        ];
    }

    protected function calculateSuccessRate(): ?float
    {
        $total = $this->success_count + $this->failure_count;

        if ($total === 0) {
            return null;
        }

        return round(($this->success_count / $total) * 100, 2);
    }

    protected function getHealthStatus(): string
    {
        $successRate = $this->calculateSuccessRate();

        if ($successRate === null) {
            return 'untested';
        }

        if ($successRate >= 95) {
            return 'healthy';
        }

        if ($successRate >= 80) {
            return 'warning';
        }

        return 'critical';
    }
}
