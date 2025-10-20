<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentConflict extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'appointment_id',
        'employee_id',
        'branch_id',
        'conflict_start',
        'conflict_end',
        'conflict_type',
        'conflict_details',
        'resolved',
        'resolved_by',
        'resolved_at',
        'resolution_notes',
    ];

    protected $casts = [
        'conflict_start' => 'datetime',
        'conflict_end' => 'datetime',
        'resolved' => 'boolean',
        'resolved_at' => 'datetime',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function isResolved(): bool
    {
        return $this->resolved;
    }

    public function isPending(): bool
    {
        return !$this->resolved;
    }

    public function isTimeOverlap(): bool
    {
        return $this->conflict_type === 'time_overlap';
    }

    public function isEmployeeUnavailable(): bool
    {
        return $this->conflict_type === 'employee_unavailable';
    }

    public function isResourceConflict(): bool
    {
        return $this->conflict_type === 'resource_conflict';
    }

    public function isOverbooking(): bool
    {
        return $this->conflict_type === 'overbooking';
    }
}
