<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceReviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'service_id' => $this->service_id,
            'customer_id' => $this->customer_id,
            'appointment_id' => $this->appointment_id,
            'rating' => (int) $this->rating,
            'review_text' => $this->review_text,
            'is_published' => (bool) $this->is_published,
            'reviewed_at' => $this->reviewed_at?->format('Y-m-d H:i:s'),
            'approved_at' => $this->approved_at?->format('Y-m-d H:i:s'),
            'rejected_at' => $this->rejected_at?->format('Y-m-d H:i:s'),
            'rejection_reason' => $this->rejection_reason,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),

            // Relationships
            'service' => ServiceResource::make($this->whenLoaded('service')),
            'customer' => CustomerResource::make($this->whenLoaded('customer')),
            'appointment' => AppointmentResource::make($this->whenLoaded('appointment')),

            // Computed fields
            'rating_stars' => str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating),
            'status' => $this->getReviewStatus(),
            'days_ago' => $this->when(
                $this->reviewed_at,
                fn() => $this->reviewed_at->diffForHumans()
            ),
        ];
    }

    private function getReviewStatus(): string
    {
        if ($this->rejected_at) {
            return 'rejected';
        }

        if ($this->approved_at && $this->is_published) {
            return 'published';
        }

        if ($this->approved_at) {
            return 'approved';
        }

        return 'pending';
    }
}
