<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentRecurrence extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $fillable = [
        'appointment_id',
        'recurrence_type',
        'frequency',
        'interval',
        'days_of_week',
        'day_of_month',
        'start_date',
        'end_date',
        'max_occurrences',
        'is_active',
    ];

    protected $casts = [
        'days_of_week' => 'array',
        'day_of_month' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'max_occurrences' => 'integer',
        'is_active' => 'boolean',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
