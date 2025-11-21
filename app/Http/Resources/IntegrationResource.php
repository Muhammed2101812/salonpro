<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IntegrationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'branch_id' => $this->branch_id,
            'integration_name' => $this->integration_name,
            'integration_type' => $this->integration_type,
            'provider' => $this->provider,
            'credentials' => $this->when(
                $request->user()?->hasRole(['super_admin', 'admin']),
                $this->credentials,
                $this->maskCredentials()
            ),
            'settings' => $this->settings,
            'is_active' => $this->is_active,
            'status' => $this->status,
            'error_message' => $this->error_message,
            'last_synced_at' => $this->last_synced_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),

            // Relationships
            'branch' => BranchResource::make($this->whenLoaded('branch')),
            'configurator' => UserResource::make($this->whenLoaded('configurator')),

            // Computed fields
            'is_connected' => $this->status === 'connected' || $this->status === 'active',
            'needs_sync' => $this->needsSync(),
            'has_credentials' => !empty($this->credentials),
            'sync_status' => $this->getSyncStatus(),
        ];
    }

    protected function maskCredentials(): array
    {
        if (empty($this->credentials)) {
            return [];
        }

        $masked = [];
        foreach ($this->credentials as $key => $value) {
            $masked[$key] = '********';
        }

        return $masked;
    }

    protected function needsSync(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->last_synced_at === null) {
            return true;
        }

        // Needs sync if last sync was more than 24 hours ago
        return $this->last_synced_at->diffInHours(now()) > 24;
    }

    protected function getSyncStatus(): string
    {
        if (!$this->is_active) {
            return 'inactive';
        }

        if ($this->last_synced_at === null) {
            return 'never_synced';
        }

        $hoursSinceSync = $this->last_synced_at->diffInHours(now());

        if ($hoursSinceSync < 1) {
            return 'synced_recently';
        }

        if ($hoursSinceSync < 24) {
            return 'synced_today';
        }

        return 'sync_needed';
    }
}
