<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardWidget extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'widget_code',
        'widget_name',
        'description',
        'widget_type',
        'chart_type',
        'data_source',
        'config',
        'refresh_interval',
        'default_width',
        'default_height',
        'is_system',
        'is_active',
    ];

    protected $casts = [
        'data_source' => 'array',
        'config' => 'array',
        'is_system' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Scope to get only active widgets
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to get widgets by type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('widget_type', $type);
    }

    /**
     * Scope to get chart widgets
     */
    public function scopeCharts($query)
    {
        return $query->where('widget_type', 'chart');
    }

    /**
     * Scope to get metric widgets
     */
    public function scopeMetrics($query)
    {
        return $query->where('widget_type', 'metric');
    }

    /**
     * Scope to get user-created widgets
     */
    public function scopeUserCreated($query)
    {
        return $query->where('is_system', false);
    }

    /**
     * Check if widget can be deleted
     */
    public function canBeDeleted(): bool
    {
        return !$this->is_system;
    }

    /**
     * Get refresh interval in seconds
     */
    public function getRefreshIntervalInSeconds(): ?int
    {
        if (!$this->refresh_interval || $this->refresh_interval === 'manual') {
            return null;
        }

        // Parse intervals like '5m', '1h', '30s'
        preg_match('/^(\d+)([smh])$/', $this->refresh_interval, $matches);

        if (!$matches) {
            return null;
        }

        $value = (int) $matches[1];
        $unit = $matches[2];

        return match ($unit) {
            's' => $value,
            'm' => $value * 60,
            'h' => $value * 3600,
            default => null,
        };
    }

    /**
     * Check if widget is a chart
     */
    public function isChart(): bool
    {
        return $this->widget_type === 'chart';
    }

    /**
     * Check if widget is a metric
     */
    public function isMetric(): bool
    {
        return $this->widget_type === 'metric';
    }

    /**
     * Get widget grid span
     */
    public function getGridSpan(): array
    {
        return [
            'width' => $this->default_width,
            'height' => $this->default_height,
        ];
    }
}
