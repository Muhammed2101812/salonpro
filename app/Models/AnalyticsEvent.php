<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class AnalyticsEvent extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id',
        'session_id',
        'branch_id',
        'event_category',
        'event_action',
        'event_label',
        'event_value',
        'page_url',
        'referrer_url',
        'resource_id',
        'resource_type',
        'metadata',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Get the user who triggered this event
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the branch for this event
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the related resource (polymorphic)
     */
    public function resource(): MorphTo
    {
        return $this->morphTo('resource');
    }

    /**
     * Scope to get events by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('event_category', $category);
    }

    /**
     * Scope to get events by action
     */
    public function scopeByAction($query, string $action)
    {
        return $query->where('event_action', $action);
    }

    /**
     * Scope to get events for a date range
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get events for today
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope to get events for current week
     */
    public function scopeThisWeek($query)
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(),
            now()->endOfWeek(),
        ]);
    }

    /**
     * Scope to get events for current month
     */
    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
    }

    /**
     * Record an event
     */
    public static function record(
        string $category,
        string $action,
        ?string $label = null,
        ?int $value = null,
        array $metadata = []
    ): self {
        $user = auth()->user();

        return static::create([
            'user_id' => $user?->id,
            'branch_id' => $user?->branch_id,
            'event_category' => $category,
            'event_action' => $action,
            'event_label' => $label,
            'event_value' => $value,
            'metadata' => $metadata,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'page_url' => request()->fullUrl(),
            'referrer_url' => request()->header('referer'),
        ]);
    }

    /**
     * Get browser from user agent
     */
    public function getBrowser(): ?string
    {
        if (!$this->user_agent) {
            return null;
        }

        $browsers = [
            'Chrome' => '/Chrome\/[\d.]+/',
            'Firefox' => '/Firefox\/[\d.]+/',
            'Safari' => '/Safari\/[\d.]+/',
            'Edge' => '/Edge\/[\d.]+/',
            'Opera' => '/Opera\/[\d.]+/',
        ];

        foreach ($browsers as $browser => $pattern) {
            if (preg_match($pattern, $this->user_agent)) {
                return $browser;
            }
        }

        return 'Unknown';
    }
}
