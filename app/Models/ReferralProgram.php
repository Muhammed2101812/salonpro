<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReferralProgram extends Model
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
        'referrer_reward_type',
        'referrer_reward_value',
        'referee_reward_type',
        'referee_reward_value',
        'min_referee_purchase',
        'max_referrals_per_customer',
        'start_date',
        'end_date',
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
            'referrer_reward_value' => 'decimal:2',
            'referee_reward_value' => 'decimal:2',
            'min_referee_purchase' => 'integer',
            'max_referrals_per_customer' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
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
     * Get the referrals for this program.
     */
    public function referrals(): HasMany
    {
        return $this->hasMany(Referral::class);
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
     * Check if customer can make more referrals.
     */
    public function canCustomerRefer(string $customerId): bool
    {
        if (!$this->max_referrals_per_customer) {
            return true;
        }

        $referralCount = $this->referrals()
            ->where('referrer_customer_id', $customerId)
            ->count();

        return $referralCount < $this->max_referrals_per_customer;
    }

    /**
     * Get total successful referrals.
     */
    public function getTotalSuccessfulReferrals(): int
    {
        return $this->referrals()
            ->whereIn('status', ['completed', 'rewarded'])
            ->count();
    }

    /**
     * Get total pending referrals.
     */
    public function getTotalPendingReferrals(): int
    {
        return $this->referrals()
            ->where('status', 'pending')
            ->count();
    }
}
