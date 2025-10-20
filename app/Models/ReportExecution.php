<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReportExecution extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'template_id',
        'schedule_id',
        'branch_id',
        'parameters',
        'status',
        'started_at',
        'completed_at',
        'execution_time_ms',
        'row_count',
        'output_file',
        'output_format',
        'file_size',
        'error_message',
        'executed_by',
    ];

    protected $casts = [
        'parameters' => 'array',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the template for this execution
     */
    public function template(): BelongsTo
    {
        return $this->belongsTo(ReportTemplate::class, 'template_id');
    }

    /**
     * Get the schedule for this execution
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(ReportSchedule::class, 'schedule_id');
    }

    /**
     * Get the branch for this execution
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who executed this report
     */
    public function executor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executed_by');
    }

    /**
     * Scope to get completed executions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope to get failed executions
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope to get running executions
     */
    public function scopeRunning($query)
    {
        return $query->where('status', 'running');
    }

    /**
     * Mark execution as started
     */
    public function markAsStarted(): bool
    {
        return $this->update([
            'status' => 'running',
            'started_at' => now(),
        ]);
    }

    /**
     * Mark execution as completed
     */
    public function markAsCompleted(string $outputFile, int $rowCount, int $fileSize): bool
    {
        $executionTime = $this->started_at ? now()->diffInMilliseconds($this->started_at) : null;

        return $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'execution_time_ms' => $executionTime,
            'row_count' => $rowCount,
            'output_file' => $outputFile,
            'file_size' => $fileSize,
        ]);
    }

    /**
     * Mark execution as failed
     */
    public function markAsFailed(string $errorMessage): bool
    {
        $executionTime = $this->started_at ? now()->diffInMilliseconds($this->started_at) : null;

        return $this->update([
            'status' => 'failed',
            'completed_at' => now(),
            'execution_time_ms' => $executionTime,
            'error_message' => $errorMessage,
        ]);
    }

    /**
     * Check if execution was successful
     */
    public function wasSuccessful(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Get execution duration in seconds
     */
    public function getExecutionTimeInSeconds(): ?float
    {
        return $this->execution_time_ms ? $this->execution_time_ms / 1000 : null;
    }

    /**
     * Get file size in human readable format
     */
    public function getHumanReadableFileSize(): ?string
    {
        if (!$this->file_size) {
            return null;
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unit = 0;

        while ($size >= 1024 && $unit < count($units) - 1) {
            $size /= 1024;
            $unit++;
        }

        return round($size, 2) . ' ' . $units[$unit];
    }
}
