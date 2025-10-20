<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyProgram extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_id',
        'program_name',
        'description',
        'point_calculation_method',
        'points_per_currency',
        'points_per_visit',
        'points_per_service',
        'min_points_to_redeem',
        'point_value',
        'points_expiry_days',
        'start_date',
        'end_date',
        'tier_levels',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'points_per_currency' => 'decimal:2',
            'points_per_visit' => 'integer',
            'points_per_service' => 'integer',
            'min_points_to_redeem' => 'integer',
            'point_value' => 'decimal:4',
            'points_expiry_days' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'tier_levels' => 'array',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the branch that owns the program.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the loyalty points for this program.
     */
    public function loyaltyPoints(): HasMany
    {
        return $this->hasMany(LoyaltyPoint::class);
    }

    /**
     * Calculate points earned from amount spent.
     */
    public function calculatePointsFromAmount(float $amount): int
    {
        if ($this->point_calculation_method !== 'amount_spent') {
            return 0;
        }

        return (int) floor($amount * (float)$this->points_per_currency);
    }

    /**
     * Calculate currency value from points.
     */
    public function calculateValueFromPoints(int $points): float
    {
        return $points * (float)$this->point_value;
    }

    /**
     * Get tier by points.
     */
    public function getTierByPoints(int $points): ?array
    {
        if (!$this->tier_levels) {
            return null;
        }

        $currentTier = null;
        $currentTierKey = null;

        foreach ($this->tier_levels as $tierKey => $tierData) {
            if ($points >= ($tierData['min'] ?? 0)) {
                $currentTier = $tierData;
                $currentTierKey = $tierKey;
            }
        }

        if ($currentTier && $currentTierKey) {
            return [
                'tier' => $currentTierKey,
                'multiplier' => $currentTier['multiplier'] ?? 1,
                'min_points' => $currentTier['min'] ?? 0,
            ];
        }

        return null;
    }

    /**
     * Check if program is currently active.
     */
    public function isCurrentlyActive(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = now()->toDateString();
        
        if ($today < $this->start_date->toDateString()) {
            return false;
        }

        if ($this->end_date && $today > $this->end_date->toDateString()) {
            return false;
        }

        return true;
    }

    /**
     * Check if points are enough to redeem.
     */
    public function canRedeem(int $points): bool
    {
        return $points >= $this->min_points_to_redeem;
    }
}
