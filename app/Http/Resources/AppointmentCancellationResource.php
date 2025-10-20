<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentCancellationResource extends JsonResource
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
            'appointment_id' => $this->appointment_id,
            'appointment' => new AppointmentResource($this->whenLoaded('appointment')),
            'reason_id' => $this->reason_id,
            'reason' => new AppointmentCancellationReasonResource($this->whenLoaded('reason')),
            'cancelled_by' => $this->cancelled_by,
            'canceller' => new UserResource($this->whenLoaded('canceller')),
            'cancelled_at' => $this->cancelled_at?->toISOString(),
            'cancellation_notes' => $this->cancellation_notes,
            'refund_issued' => $this->refund_issued,
            'refund_amount' => $this->refund_amount,
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}
