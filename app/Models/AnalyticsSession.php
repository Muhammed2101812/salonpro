<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalyticsSession extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'branch_id',
        'session_id',
        'started_at',
        'ended_at',
        'duration_seconds',
        'page_views',
        'events_count',
        'entry_page',
        'exit_page',
        'device_type',
        'browser',
        'platform',
        'ip_address',
        'metadata',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the user for this session
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the branch for this session
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Scope to get active sessions
     */
    public function scopeActive($query)
    {
        return $query->whereNull('ended_at');
    }

    /**
     * Scope to get completed sessions
     */
    public function scopeCompleted($query)
    {
        return $query->whereNotNull('ended_at');
    }

    /**
     * Scope to get sessions for a date range
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('started_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get sessions by device type
     */
    public function scopeByDeviceType($query, string $deviceType)
    {
        return $query->where('device_type', $deviceType);
    }

    /**
     * Scope to get sessions by browser
     */
    public function scopeByBrowser($query, string $browser)
    {
        return $query->where('browser', $browser);
    }

    /**
     * End the session
     */
    public function end(): bool
    {
        $duration = $this->started_at->diffInSeconds(now());

        return $this->update([
            'ended_at' => now(),
            'duration_seconds' => $duration,
        ]);
    }

    /**
     * Increment page views
     */
    public function incrementPageViews(): bool
    {
        return $this->increment('page_views');
    }

    /**
     * Increment events count
     */
    public function incrementEventsCount(): bool
    {
        return $this->increment('events_count');
    }

    /**
     * Update exit page
     */
    public function updateExitPage(string $page): bool
    {
        return $this->update(['exit_page' => $page]);
    }

    /**
     * Get session duration in human readable format
     */
    public function getHumanReadableDuration(): ?string
    {
        if (!$this->duration_seconds) {
            return null;
        }

        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);
        $seconds = $this->duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $seconds);
        }

        if ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $seconds);
        }

        return sprintf('%ds', $seconds);
    }

    /**
     * Check if session is active
     */
    public function isActive(): bool
    {
        return $this->ended_at === null;
    }

    /**
     * Get average time per page
     */
    public function getAverageTimePerPage(): ?float
    {
        if (!$this->page_views || !$this->duration_seconds) {
            return null;
        }

        return round($this->duration_seconds / $this->page_views, 2);
    }
}
