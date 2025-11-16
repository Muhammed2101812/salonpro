<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentHistoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'appointment_id' => $this->appointment_id,
            'user_id' => $this->user_id,
            'action' => $this->action,
            'old_values' => $this->old_values,
            'new_values' => $this->new_values,
            'ip_address' => $this->when(
                auth()->user()?->can('viewSensitiveData', $this->resource),
                $this->ip_address
            ),
            'user_agent' => $this->user_agent,
            'changed_at' => $this->changed_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),

            // Relationships
            'appointment' => AppointmentResource::make($this->whenLoaded('appointment')),
            'user' => UserResource::make($this->whenLoaded('user')),

            // Computed fields
            'action_label' => $this->getActionLabel(),
            'time_ago' => $this->when(
                $this->changed_at,
                fn() => $this->changed_at->diffForHumans()
            ),
            'changes_summary' => $this->getChangesSummary(),
            'browser' => $this->when(
                $this->user_agent,
                fn() => $this->parseBrowser()
            ),
        ];
    }

    private function getActionLabel(): string
    {
        return match($this->action) {
            'created' => 'Created',
            'updated' => 'Updated',
            'deleted' => 'Deleted',
            'restored' => 'Restored',
            'status_changed' => 'Status Changed',
            'rescheduled' => 'Rescheduled',
            'cancelled' => 'Cancelled',
            'completed' => 'Completed',
            default => ucfirst(str_replace('_', ' ', $this->action)),
        };
    }

    private function getChangesSummary(): ?array
    {
        if (!$this->old_values || !$this->new_values) {
            return null;
        }

        $changes = [];
        foreach ($this->new_values as $key => $newValue) {
            $oldValue = $this->old_values[$key] ?? null;
            if ($oldValue !== $newValue) {
                $changes[] = [
                    'field' => $key,
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }

    private function parseBrowser(): ?string
    {
        if (!$this->user_agent) {
            return null;
        }

        // Simple browser detection
        if (str_contains($this->user_agent, 'Chrome')) {
            return 'Chrome';
        }
        if (str_contains($this->user_agent, 'Firefox')) {
            return 'Firefox';
        }
        if (str_contains($this->user_agent, 'Safari')) {
            return 'Safari';
        }
        if (str_contains($this->user_agent, 'Edge')) {
            return 'Edge';
        }

        return 'Unknown';
    }
}
