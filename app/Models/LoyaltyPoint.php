<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LoyaltyPoint extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loyalty_program_id',
        'customer_id',
        'total_points_earned',
        'total_points_redeemed',
        'current_balance',
        'points_expiring_soon',
        'current_tier',
        'tier_multiplier',
        'last_activity_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_points_earned' => 'integer',
            'total_points_redeemed' => 'integer',
            'current_balance' => 'integer',
            'points_expiring_soon' => 'integer',
            'tier_multiplier' => 'decimal:2',
            'last_activity_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the loyalty program.
     */
    public function loyaltyProgram(): BelongsTo
    {
        return $this->belongsTo(LoyaltyProgram::class);
    }

    /**
     * Get the customer.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the transactions for this loyalty point.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(LoyaltyPointTransaction::class, 'loyalty_points_id');
    }

    /**
     * Add points.
     */
    public function addPoints(int $points, string $type = 'earned', ?string $referenceId = null, ?string $referenceType = null, ?string $description = null, ?string $createdBy = null, ?string $expiryDate = null): void
    {
        $this->total_points_earned += $points;
        $this->current_balance += $points;
        $this->last_activity_at = now();
        
        // Update tier
        $this->updateTier();
        
        $this->save();

        // Create transaction
        LoyaltyPointTransaction::create([
            'loyalty_points_id' => $this->id,
            'transaction_type' => $type,
            'points' => $points,
            'balance_after' => $this->current_balance,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'description' => $description,
            'expiry_date' => $expiryDate,
            'created_by' => $createdBy,
        ]);
    }

    /**
     * Redeem points.
     */
    public function redeemPoints(int $points, ?string $referenceId = null, ?string $referenceType = null, ?string $description = null, ?string $createdBy = null): bool
    {
        if ($this->current_balance < $points) {
            return false;
        }

        if (!$this->loyaltyProgram->canRedeem($points)) {
            return false;
        }

        $this->total_points_redeemed += $points;
        $this->current_balance -= $points;
        $this->last_activity_at = now();
        
        // Update tier
        $this->updateTier();
        
        $this->save();

        // Create transaction
        LoyaltyPointTransaction::create([
            'loyalty_points_id' => $this->id,
            'transaction_type' => 'redeemed',
            'points' => -$points,
            'balance_after' => $this->current_balance,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'description' => $description,
            'created_by' => $createdBy,
        ]);

        return true;
    }

    /**
     * Update tier based on total points earned.
     */
    public function updateTier(): void
    {
        $tierInfo = $this->loyaltyProgram->getTierByPoints($this->total_points_earned);
        
        if ($tierInfo) {
            $this->current_tier = $tierInfo['tier'];
            $this->tier_multiplier = $tierInfo['multiplier'];
        }
    }

    /**
     * Calculate points value in currency.
     */
    public function getPointsValue(): float
    {
        return $this->loyaltyProgram->calculateValueFromPoints($this->current_balance);
    }

    /**
     * Expire points.
     */
    public function expirePoints(int $points, ?string $description = null): void
    {
        if ($this->current_balance < $points) {
            $points = $this->current_balance;
        }

        $this->current_balance -= $points;
        $this->save();

        // Create transaction
        LoyaltyPointTransaction::create([
            'loyalty_points_id' => $this->id,
            'transaction_type' => 'expired',
            'points' => -$points,
            'balance_after' => $this->current_balance,
            'description' => $description ?? 'Points expired',
        ]);
    }
}
