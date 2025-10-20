<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KpiDefinition extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'kpi_code',
        'kpi_name',
        'description',
        'category',
        'calculation_method',
        'calculation_formula',
        'unit',
        'frequency',
        'target_value',
        'warning_threshold',
        'critical_threshold',
        'higher_is_better',
        'is_active',
    ];

    protected $casts = [
        'target_value' => 'decimal:2',
        'warning_threshold' => 'decimal:2',
        'critical_threshold' => 'decimal:2',
        'higher_is_better' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get all values for this KPI
     */
    public function values(): HasMany
    {
        return $this->hasMany(KpiValue::class, 'kpi_definition_id');
    }

    /**
     * Scope to get only active KPIs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get KPIs by category
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope to get KPIs by frequency
     */
    public function scopeByFrequency($query, string $frequency)
    {
        return $query->where('frequency', $frequency);
    }

    /**
     * Get latest value for this KPI
     */
    public function latestValue()
    {
        return $this->values()->latest('period_end')->first();
    }

    /**
     * Get values for a specific branch
     */
    public function getValuesForBranch(string $branchId)
    {
        return $this->values()->where('branch_id', $branchId)->get();
    }

    /**
     * Calculate performance status based on actual value
     */
    public function calculatePerformanceStatus(float $actualValue): string
    {
        if (!$this->target_value) {
            return 'good';
        }

        $variance = $actualValue - $this->target_value;
        $isPositive = $this->higher_is_better ? $variance >= 0 : $variance <= 0;

        if ($isPositive) {
            return 'excellent';
        }

        $absVariance = abs($variance);

        if ($this->critical_threshold && $absVariance >= abs($this->critical_threshold)) {
            return 'critical';
        }

        if ($this->warning_threshold && $absVariance >= abs($this->warning_threshold)) {
            return 'warning';
        }

        return 'good';
    }

    /**
     * Get trend direction for this KPI
     */
    public function getTrend(int $periods = 3): ?string
    {
        $values = $this->values()
            ->orderBy('period_end', 'desc')
            ->limit($periods)
            ->pluck('actual_value')
            ->toArray();

        if (count($values) < 2) {
            return null;
        }

        $values = array_reverse($values);
        $trend = $values[count($values) - 1] - $values[0];

        if ($trend > 0) {
            return 'up';
        } elseif ($trend < 0) {
            return 'down';
        }

        return 'stable';
    }
}
