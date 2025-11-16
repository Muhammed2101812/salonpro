<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentConflict extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'appointment_id',
        'conflicting_appointment_id',
        'conflict_type',
        'severity',
        'description',
        'detected_at',
        'resolved_at',
        'resolution_method',
        'notes',
    ];

    protected $casts = [
        'detected_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function conflictingAppointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class, 'conflicting_appointment_id');
    }
}
