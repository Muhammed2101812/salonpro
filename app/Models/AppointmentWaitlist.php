<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppointmentWaitlist extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $table = 'appointment_waitlist';

    protected $fillable = [
        'branch_id',
        'customer_id',
        'service_id',
        'employee_id',
        'preferred_date',
        'preferred_time_start',
        'preferred_time_end',
        'is_flexible',
        'priority',
        'status',
        'notes',
        'notified_at',
        'converted_at',
        'appointment_id',
    ];

    protected $casts = [
        'preferred_date' => 'date',
        'is_flexible' => 'boolean',
        'priority' => 'integer',
        'notified_at' => 'datetime',
        'converted_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

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

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }
}
