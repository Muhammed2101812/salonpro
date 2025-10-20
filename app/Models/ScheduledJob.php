<?php

namespace App\Models;

use Cron\CronExpression;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScheduledJob extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'job_name',
        'job_class',
        'cron_expression',
        'parameters',
        'is_active',
        'last_run_at',
        'next_run_at',
        'run_count',
        'failure_count',
        'last_error',
    ];

    protected $casts = [
        'parameters' => 'array',
        'is_active' => 'boolean',
        'last_run_at' => 'datetime',
        'next_run_at' => 'datetime',
        'run_count' => 'integer',
        'failure_count' => 'integer',
    ];

    /**
     * Get all execution logs for this job.
     */
    public function executionLogs(): HasMany
    {
        return $this->hasMany(JobExecutionLog::class);
    }

    /**
     * Scope to get active jobs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get jobs that are due to run.
     */
    public function scopeDue($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('next_run_at')
                    ->orWhere('next_run_at', '<=', now());
            });
    }

    /**
     * Calculate next run time based on cron expression.
     */
    public function calculateNextRunTime(): \DateTime
    {
        $cron = new CronExpression($this->cron_expression);
        return $cron->getNextRunDate();
    }

    /**
     * Update next run time.
     */
    public function updateNextRunTime(): void
    {
        $this->update([
            'next_run_at' => $this->calculateNextRunTime(),
        ]);
    }

    /**
     * Mark job as started.
     */
    public function markAsStarted(): JobExecutionLog
    {
        $this->increment('run_count');
        $this->update(['last_run_at' => now()]);

        return JobExecutionLog::create([
            'scheduled_job_id' => $this->id,
            'job_class' => $this->job_class,
            'parameters' => $this->parameters,
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    /**
     * Mark job as completed.
     */
    public function markAsCompleted(JobExecutionLog $executionLog, ?string $output = null): void
    {
        $executionLog->markAsCompleted($output);
        $this->updateNextRunTime();
    }

    /**
     * Mark job as failed.
     */
    public function markAsFailed(JobExecutionLog $executionLog, string $errorMessage): void
    {
        $executionLog->markAsFailed($errorMessage);
        $this->increment('failure_count');
        $this->update(['last_error' => $errorMessage]);
        $this->updateNextRunTime();
    }

    /**
     * Check if job is due to run.
     */
    public function isDue(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->next_run_at) {
            return true;
        }

        return $this->next_run_at->isPast();
    }

    /**
     * Get success rate percentage.
     */
    public function getSuccessRateAttribute(): float
    {
        if ($this->run_count === 0) {
            return 100;
        }

        $successCount = $this->run_count - $this->failure_count;
        return round(($successCount / $this->run_count) * 100, 2);
    }

    /**
     * Get the last execution log.
     */
    public function getLastExecutionAttribute(): ?JobExecutionLog
    {
        return $this->executionLogs()->latest('started_at')->first();
    }

    /**
     * Enable the job.
     */
    public function enable(): void
    {
        $this->update([
            'is_active' => true,
            'next_run_at' => $this->calculateNextRunTime(),
        ]);
    }

    /**
     * Disable the job.
     */
    public function disable(): void
    {
        $this->update(['is_active' => false]);
    }
}
