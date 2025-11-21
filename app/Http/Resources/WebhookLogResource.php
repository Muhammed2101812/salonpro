<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WebhookLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'webhook_id' => $this->webhook_id,
            'event' => $this->event,
            'payload' => $this->payload,
            'http_status' => $this->http_status,
            'response_body' => $this->response_body,
            'status' => $this->status,
            'attempt' => $this->attempt,
            'duration_ms' => $this->duration_ms,
            'error_message' => $this->error_message,
            'sent_at' => $this->sent_at?->toIso8601String(),
            'next_retry_at' => $this->next_retry_at?->toIso8601String(),
            'created_at' => $this->created_at->toIso8601String(),

            // Relationships
            'webhook' => WebhookResource::make($this->whenLoaded('webhook')),

            // Computed fields
            'is_successful' => $this->status === 'success',
            'is_failed' => $this->status === 'failed',
            'is_pending' => $this->status === 'pending',
            'can_retry' => $this->canRetry(),
            'retry_available_at' => $this->getRetryAvailableAt(),
            'duration_formatted' => $this->formatDuration(),
        ];
    }

    protected function canRetry(): bool
    {
        if ($this->status === 'success') {
            return false;
        }

        if (!$this->webhook) {
            return false;
        }

        return $this->attempt < $this->webhook->max_retries;
    }

    protected function getRetryAvailableAt(): ?string
    {
        if (!$this->canRetry()) {
            return null;
        }

        if ($this->next_retry_at && $this->next_retry_at->isFuture()) {
            return $this->next_retry_at->toIso8601String();
        }

        return 'now';
    }

    protected function formatDuration(): ?string
    {
        if ($this->duration_ms === null) {
            return null;
        }

        if ($this->duration_ms < 1000) {
            return $this->duration_ms . 'ms';
        }

        return round($this->duration_ms / 1000, 2) . 's';
    }
}
