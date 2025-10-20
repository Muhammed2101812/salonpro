<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentWaitlist extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'appointment_waitlist';

    protected $fillable = [
        'customer_id',
        'service_id',
        'employee_id',
        'branch_id',
        'preferred_date',
        'preferred_time_start',
        'preferred_time_end',
        'preferred_days',
        'priority',
        'status',
        'notified_at',
        'expires_at',
        'notes',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'preferred_days' => 'array',
        'notified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function isWaiting(): bool
    {
        return $this->status === 'waiting';
    }

    public function isNotified(): bool
    {
        return $this->status === 'notified';
    }

    public function isBooked(): bool
    {
        return $this->status === 'booked';
    }

    public function isExpired(): bool
    {
        return $this->status === 'expired' || 
               ($this->expires_at && $this->expires_at->isPast());
    }

    public function isHighPriority(): bool
    {
        return $this->priority === 'high';
    }
}
