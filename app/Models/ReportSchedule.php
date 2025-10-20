<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportSchedule extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'template_id',
        'branch_id',
        'schedule_name',
        'frequency',
        'schedule_config',
        'parameters',
        'recipients',
        'last_run_at',
        'next_run_at',
        'status',
        'is_active',
        'created_by',
    ];

    protected $casts = [
        'schedule_config' => 'array',
        'parameters' => 'array',
        'recipients' => 'array',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the template for this schedule
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ReportTemplate::class, 'template_id');
    }

    /**
     * Get the branch for this schedule
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created this schedule
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get all executions for this schedule
     */
    public function executions(): HasMany
    {
        return $this->hasMany(ReportExecution::class, 'schedule_id');
    }

    /**
     * Scope to get only active schedules
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('status', 'active');
    }

    /**
     * Scope to get schedules due for execution
     */
    public function scopeDueForExecution($query)
    {
        return $query->active()
            ->where('next_run_at', '<=', now());
    }

    /**
     * Scope to get schedules by frequency
     */
    public function scopeByFrequency($query, string $frequency)
    {
        return $query->where('frequency', $frequency);
    }

    /**
     * Mark schedule as executed
     */
    public function markAsExecuted(): void
    {
        $this->update([
            'last_run_at' => now(),
            'next_run_at' => $this->calculateNextRunTime(),
        ]);
    }

    /**
     * Calculate next run time based on frequency
     */
    public function calculateNextRunTime()
    {
        $scheduleConfig = $this->schedule_config;
        $now = now();

        return match ($this->frequency) {
            'daily' => $now->addDay()->setTimeFromTimeString($scheduleConfig['time'] ?? '00:00'),
            'weekly' => $now->addWeek()->setTimeFromTimeString($scheduleConfig['time'] ?? '00:00'),
            'monthly' => $now->addMonth()->setTimeFromTimeString($scheduleConfig['time'] ?? '00:00'),
            'quarterly' => $now->addMonths(3)->setTimeFromTimeString($scheduleConfig['time'] ?? '00:00'),
            'yearly' => $now->addYear()->setTimeFromTimeString($scheduleConfig['time'] ?? '00:00'),
            default => $now->addDay(),
        };
    }

    /**
     * Pause the schedule
     */
    public function pause(): bool
    {
        return $this->update(['status' => 'paused']);
    }

    /**
     * Resume the schedule
     */
    public function resume(): bool
    {
        return $this->update(['status' => 'active']);
    }
}
