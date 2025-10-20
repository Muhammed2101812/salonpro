<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiValue extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'kpi_definition_id',
        'branch_id',
        'period_start',
        'period_end',
        'actual_value',
        'target_value',
        'variance',
        'variance_percentage',
        'performance_status',
        'notes',
        'calculated_at',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'actual_value' => 'decimal:2',
        'target_value' => 'decimal:2',
        'variance' => 'decimal:2',
        'variance_percentage' => 'decimal:2',
        'calculated_at' => 'datetime',
    ];

    /**
     * Get the KPI definition for this value
     */
    public function definition(): BelongsTo
    {
        return $this->belongsTo(KpiDefinition::class, 'kpi_definition_id');
    }

    /**
     * Get the branch for this value
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Scope to get values by performance status
     */
    public function scopeByPerformanceStatus($query, string $status)
    {
        return $query->where('performance_status', $status);
    }

    /**
     * Scope to get excellent performance values
     */
    public function scopeExcellent($query)
    {
        return $query->where('performance_status', 'excellent');
    }

    /**
     * Scope to get critical performance values
     */
    public function scopeCritical($query)
    {
        return $query->where('performance_status', 'critical');
    }

    /**
     * Scope to get values for a date range
     */
    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->where('period_start', '>=', $startDate)
            ->where('period_end', '<=', $endDate);
    }

    /**
     * Calculate variance from target
     */
    public function calculateVariance(): void
    {
        if ($this->target_value) {
            $variance = $this->actual_value - $this->target_value;
            $variancePercentage = ($variance / $this->target_value) * 100;

            $this->variance = $variance;
            $this->variance_percentage = $variancePercentage;
        }
    }

    /**
     * Update performance status based on definition
     */
    public function updatePerformanceStatus(): void
    {
        if ($this->definition) {
            $this->performance_status = $this->definition->calculatePerformanceStatus($this->actual_value);
        }
    }

    /**
     * Check if target was met
     */
    public function targetMet(): bool
    {
        if (!$this->target_value) {
            return true;
        }

        if ($this->definition->higher_is_better) {
            return $this->actual_value >= $this->target_value;
        }

        return $this->actual_value <= $this->target_value;
    }

    /**
     * Get performance indicator
     */
    public function getPerformanceIndicator(): string
    {
        return match ($this->performance_status) {
            'excellent' => '🟢',
            'good' => '🟡',
            'warning' => '🟠',
            'critical' => '🔴',
            default => '⚪',
        };
    }
}
