<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppointmentRecurrence extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'customer_id',
        'service_id',
        'employee_id',
        'branch_id',
        'preferred_time',
        'recurrence_type',
        'recurrence_pattern',
        'start_date',
        'end_date',
        'occurrences',
        'is_active',
        'notes',
    ];

    protected $casts = [
        'recurrence_pattern' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'occurrences' => 'integer',
        'is_active' => 'boolean',
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

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class, 'recurrence_id');
    }
}
