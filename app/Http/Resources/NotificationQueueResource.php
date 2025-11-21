<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NotificationQueueResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'recipient_type' => $this->recipient_type,
            'recipient_id' => $this->recipient_id,
            'channel' => $this->channel,
            'template_id' => $this->template_id,
            'subject' => $this->subject,
            'content' => $this->content,
            'data' => $this->data,
            'status' => $this->status,
            'priority' => $this->priority,
            'scheduled_at' => $this->scheduled_at?->format('Y-m-d H:i:s'),
            'sent_at' => $this->sent_at?->format('Y-m-d H:i:s'),
            'failed_at' => $this->failed_at?->format('Y-m-d H:i:s'),
            'error_message' => $this->when(
                $this->status === 'failed',
                $this->error_message
            ),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships
            'template' => NotificationTemplateResource::make($this->whenLoaded('template')),
            'recipient' => $this->when(
                $this->relationLoaded('recipient'),
                fn() => $this->recipient
            ),

            // Computed fields
            'channel_icon' => $this->getChannelIcon(),
            'priority_label' => ucfirst($this->priority),
            'status_badge' => $this->getStatusBadge(),
            'is_scheduled' => $this->scheduled_at && $this->scheduled_at->isFuture(),
            'can_retry' => $this->status === 'failed',
        ];
    }

    private function getChannelIcon(): string
    {
        return match($this->channel) {
            'email' => '📧',
            'sms' => '📱',
            'push' => '🔔',
            default => '📨',
        };
    }

    private function getStatusBadge(): array
    {
        return match($this->status) {
            'pending' => ['color' => 'warning', 'label' => 'Pending'],
            'sent' => ['color' => 'success', 'label' => 'Sent'],
            'failed' => ['color' => 'danger', 'label' => 'Failed'],
            default => ['color' => 'secondary', 'label' => ucfirst($this->status)],
        };
    }
}
