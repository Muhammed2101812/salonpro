<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class FeatureFlag extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'feature_key',
        'feature_name',
        'description',
        'is_enabled',
        'conditions',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'is_enabled' => 'boolean',
        'conditions' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Scope to get enabled features.
     */
    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    /**
     * Scope to get active features (considering date range).
     */
    public function scopeActive($query)
    {
        return $query->where('is_enabled', true)
            ->where(function ($q) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    /**
     * Check if a feature is enabled.
     */
    public static function isEnabled(string $featureKey): bool
    {
        return Cache::remember("feature_flag_{$featureKey}", 3600, function () use ($featureKey) {
            $feature = self::where('feature_key', $featureKey)->first();

            if (!$feature) {
                return false;
            }

            return $feature->checkIfEnabled();
        });
    }

    /**
     * Check if this feature is currently enabled.
     */
    public function checkIfEnabled(): bool
    {
        if (!$this->is_enabled) {
            return false;
        }

        // Check date range
        if ($this->start_date && $this->start_date->isFuture()) {
            return false;
        }

        if ($this->end_date && $this->end_date->isPast()) {
            return false;
        }

        // Check conditions if any
        if ($this->conditions) {
            return $this->evaluateConditions();
        }

        return true;
    }

    /**
     * Evaluate conditions for enabling the feature.
     */
    protected function evaluateConditions(): bool
    {
        // This can be extended based on your needs
        // For example, check user roles, branch, etc.
        return true;
    }

    /**
     * Enable this feature.
     */
    public function enable(): void
    {
        $this->update(['is_enabled' => true]);
        $this->clearCache();
    }

    /**
     * Disable this feature.
     */
    public function disable(): void
    {
        $this->update(['is_enabled' => false]);
        $this->clearCache();
    }

    /**
     * Toggle this feature.
     */
    public function toggle(): void
    {
        $this->update(['is_enabled' => !$this->is_enabled]);
        $this->clearCache();
    }

    /**
     * Clear cache for this feature.
     */
    public function clearCache(): void
    {
        Cache::forget("feature_flag_{$this->feature_key}");
    }

    /**
     * Boot method to clear cache on update.
     */
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($feature) {
            $feature->clearCache();
        });

        static::deleted(function ($feature) {
            $feature->clearCache();
        });
    }
}
