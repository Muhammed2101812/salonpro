<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceMetric extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'branch_id',
        'metric_name',
        'metric_category',
        'metric_value',
        'unit',
        'metadata',
        'measured_at',
    ];

    protected $casts = [
        'metric_value' => 'decimal:4',
        'metadata' => 'array',
        'measured_at' => 'datetime',
    ];

    /**
     * Get the branch for this metric
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Scope to get metrics by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('metric_category', $category);
    }

    /**
     * Scope to get metrics by name
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('metric_name', $name);
    }

    /**
     * Scope to get metrics for a date range
     */
    public function scopeForDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('measured_at', [$startDate, $endDate]);
    }

    /**
     * Scope to get database metrics
     */
    public function scopeDatabase($query)
    {
        return $query->where('metric_category', 'database');
    }

    /**
     * Scope to get API metrics
     */
    public function scopeApi($query)
    {
        return $query->where('metric_category', 'api');
    }

    /**
     * Scope to get queue metrics
     */
    public function scopeQueue($query)
    {
        return $query->where('metric_category', 'queue');
    }

    /**
     * Scope to get cache metrics
     */
    public function scopeCache($query)
    {
        return $query->where('metric_category', 'cache');
    }

    /**
     * Record a metric
     */
    public static function record(
        string $name,
        string $category,
        float $value,
        string $unit,
        ?string $branchId = null,
        array $metadata = []
    ): self {
        return static::create([
            'branch_id' => $branchId ?? auth()->user()?->branch_id,
            'metric_name' => $name,
            'metric_category' => $category,
            'metric_value' => $value,
            'unit' => $unit,
            'metadata' => $metadata,
            'measured_at' => now(),
        ]);
    }

    /**
     * Get average value for a metric
     */
    public static function getAverage(string $name, string $category, $startDate, $endDate): ?float
    {
        return static::byName($name)
            ->byCategory($category)
            ->forDateRange($startDate, $endDate)
            ->avg('metric_value');
    }

    /**
     * Get max value for a metric
     */
    public static function getMax(string $name, string $category, $startDate, $endDate): ?float
    {
        return static::byName($name)
            ->byCategory($category)
            ->forDateRange($startDate, $endDate)
            ->max('metric_value');
    }

    /**
     * Get min value for a metric
     */
    public static function getMin(string $name, string $category, $startDate, $endDate): ?float
    {
        return static::byName($name)
            ->byCategory($category)
            ->forDateRange($startDate, $endDate)
            ->min('metric_value');
    }

    /**
     * Get formatted value with unit
     */
    public function getFormattedValue(): string
    {
        return number_format($this->metric_value, 2) . ' ' . $this->unit;
    }
}
