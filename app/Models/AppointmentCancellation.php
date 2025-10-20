<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentCancellation extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'appointment_id',
        'reason_id',
        'cancelled_by',
        'cancelled_at',
        'cancellation_notes',
        'refund_issued',
        'refund_amount',
    ];

    protected $casts = [
        'cancelled_at' => 'datetime',
        'refund_issued' => 'boolean',
        'refund_amount' => 'decimal:2',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(AppointmentCancellationReason::class, 'reason_id');
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function hasRefund(): bool
    {
        return $this->refund_issued;
    }

    public function hasReason(): bool
    {
        return $this->reason_id !== null;
    }

    public function isCustomerFault(): bool
    {
        return $this->reason && $this->reason->is_customer_fault;
    }

    public function scopeWithRefund($query)
    {
        return $query->where('refund_issued', true);
    }

    public function scopeWithoutRefund($query)
    {
        return $query->where('refund_issued', false);
    }

    public function scopeByReason($query, string $reasonId)
    {
        return $query->where('reason_id', $reasonId);
    }
}
