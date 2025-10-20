<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JobExecutionLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'scheduled_job_id',
        'job_class',
        'parameters',
        'status',
        'started_at',
        'completed_at',
        'duration_ms',
        'output',
        'error_message',
    ];

    protected $casts = [
        'parameters' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'duration_ms' => 'integer',
    ];

    /**
     * Get the scheduled job.
     */
    public function scheduledJob(): BelongsTo
    {
        return $this->belongsTo(ScheduledJob::class);
    }

    /**
     * Scope to filter by status.
     */
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope to get completed executions.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get failed executions.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('started_at', [$startDate, $endDate]);
    }

    /**
     * Mark execution as completed.
     */
    public function markAsCompleted(?string $output = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'duration_ms' => $this->started_at->diffInMilliseconds(now()),
            'output' => $output,
        ]);
    }

    /**
     * Mark execution as failed.
     */
    public function markAsFailed(string $errorMessage): void
    {
        $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'duration_ms' => $this->started_at->diffInMilliseconds(now()),
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Check if execution is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if execution is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Check if execution is running.
     */
    public function isRunning(): bool
    {
        return $this->status === 'running';
    }

    /**
     * Get duration in seconds.
     */
    public function getDurationInSecondsAttribute(): float
    {
        if (!$this->duration_ms) {
            return 0;
        }

        return round($this->duration_ms / 1000, 2);
    }

    /**
     * Get human-readable duration.
     */
    public function getHumanDurationAttribute(): string
    {
        if (!$this->duration_ms) {
            return 'N/A';
        }

        $seconds = $this->duration_in_seconds;

        if ($seconds < 1) {
            return $this->duration_ms . 'ms';
        }

        if ($seconds < 60) {
            return $seconds . 's';
        }

        $minutes = floor($seconds / 60);
        $seconds = $seconds % 60;

        return $minutes . 'm ' . $seconds . 's';
    }
}
