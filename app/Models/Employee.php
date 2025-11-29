<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = ['name'];

    protected $fillable = [
        'branch_id',
        'user_id',
        'first_name',
        'last_name',
        'phone',
        'email',
        'specialties',
        'commission_rate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'specialties' => 'array',
            'commission_rate' => 'decimal:2',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function appointmentRecurrences(): HasMany
    {
        return $this->hasMany(AppointmentRecurrence::class);
    }

    public function appointmentGroups(): HasMany
    {
        return $this->hasMany(AppointmentGroup::class);
    }

    public function waitlistEntries(): HasMany
    {
        return $this->hasMany(AppointmentWaitlist::class);
    }

    public function conflicts(): HasMany
    {
        return $this->hasMany(AppointmentConflict::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    /**
     * Get the employee's name (alias for full_name).
     */
    public function getNameAttribute(): string
    {
        return $this->getFullNameAttribute();
    }
}
