<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentHistory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'appointment_id',
        'user_id',
        'action',
        'old_data',
        'new_data',
        'ip_address',
        'notes',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isCreated(): bool
    {
        return $this->action === 'created';
    }

    public function isUpdated(): bool
    {
        return $this->action === 'updated';
    }

    public function isCancelled(): bool
    {
        return $this->action === 'cancelled';
    }

    public function isRescheduled(): bool
    {
        return $this->action === 'rescheduled';
    }

    public function isCompleted(): bool
    {
        return $this->action === 'completed';
    }

    public function isNoShow(): bool
    {
        return $this->action === 'no_show';
    }

    public function isConfirmed(): bool
    {
        return $this->action === 'confirmed';
    }

    public function getChanges(): array
    {
        if (!$this->old_data || !$this->new_data) {
            return [];
        }

        $changes = [];
        foreach ($this->new_data as $key => $newValue) {
            $oldValue = $this->old_data[$key] ?? null;
            if ($oldValue !== $newValue) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        return $changes;
    }
}
