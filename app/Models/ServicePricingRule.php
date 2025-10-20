<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePricingRule extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'service_id',
        'rule_name',
        'rule_type',
        'conditions',
        'adjustment_type',
        'adjustment_value',
        'priority',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'conditions' => 'array',
            'adjustment_value' => 'decimal:2',
            'priority' => 'integer',
            'valid_from' => 'date',
            'valid_until' => 'date',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Check if the rule is currently valid based on date range
     */
    public function isCurrentlyValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $now = now();

        if ($this->valid_from && $now->isBefore($this->valid_from)) {
            return false;
        }

        if ($this->valid_until && $now->isAfter($this->valid_until)) {
            return false;
        }

        return true;
    }

    /**
     * Calculate adjusted price based on the rule
     */
    public function calculateAdjustedPrice(float $basePrice): float
    {
        if ($this->adjustment_type === 'percentage') {
            $adjustment = $basePrice * ($this->adjustment_value / 100);
            return round($basePrice + $adjustment, 2);
        }

        // fixed adjustment
        return round($basePrice + $this->adjustment_value, 2);
    }

    /**
     * Scope to get active rules ordered by priority
     */
    public function scopeActivePrioritized($query)
    {
        return $query->where('is_active', true)
            ->orderBy('priority', 'desc');
    }
}
