<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentCancellation extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'appointment_id',
        'cancelled_by',
        'cancellation_reason_id',
        'cancellation_date',
        'refund_amount',
        'refund_status',
        'notes',
    ];

    protected $casts = [
        'cancellation_date' => 'datetime',
        'refund_amount' => 'decimal:2',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function reason(): BelongsTo
    {
        return $this->belongsTo(AppointmentCancellationReason::class, 'cancellation_reason_id');
    }
}
