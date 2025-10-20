<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppointmentGroup extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'branch_id',
        'group_name',
        'max_participants',
        'current_participants',
        'service_id',
        'employee_id',
        'scheduled_at',
        'duration',
        'price_per_person',
        'status',
        'notes',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'max_participants' => 'integer',
        'current_participants' => 'integer',
        'duration' => 'integer',
        'price_per_person' => 'decimal:2',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function participants(): HasMany
    {
        return $this->hasMany(AppointmentGroupParticipant::class, 'group_id');
    }

    public function isFull(): bool
    {
        return $this->current_participants >= $this->max_participants;
    }

    public function hasAvailableSlots(): bool
    {
        return $this->current_participants < $this->max_participants;
    }

    public function availableSlots(): int
    {
        return max(0, $this->max_participants - $this->current_participants);
    }
}
