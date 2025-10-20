<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Referral extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'referral_program_id',
        'referrer_customer_id',
        'referee_customer_id',
        'referee_name',
        'referee_phone',
        'referee_email',
        'referral_code',
        'status',
        'referred_at',
        'registered_at',
        'completed_at',
        'rewarded_at',
        'referee_first_purchase',
        'referrer_rewarded',
        'referee_rewarded',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'referred_at' => 'datetime',
            'registered_at' => 'datetime',
            'completed_at' => 'datetime',
            'rewarded_at' => 'datetime',
            'referee_first_purchase' => 'decimal:2',
            'referrer_rewarded' => 'boolean',
            'referee_rewarded' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($referral) {
            if (!$referral->referral_code) {
                $referral->referral_code = $referral->generateUniqueReferralCode();
            }
            if (!$referral->referred_at) {
                $referral->referred_at = now();
            }
        });
    }

    /**
     * Get the referral program.
     */
    public function referralProgram(): BelongsTo
    {
        return $this->belongsTo(ReferralProgram::class);
    }

    /**
     * Get the referrer customer.
     */
    public function referrer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referrer_customer_id');
    }

    /**
     * Get the referee customer.
     */
    public function referee(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'referee_customer_id');
    }

    /**
     * Generate unique referral code.
     */
    protected function generateUniqueReferralCode(): string
    {
        do {
            $code = 'REF-' . strtoupper(Str::random(8));
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    /**
     * Mark as registered.
     */
    public function markAsRegistered(string $customerId): void
    {
        $this->referee_customer_id = $customerId;
        $this->status = 'registered';
        $this->registered_at = now();
        $this->save();
    }

    /**
     * Mark as completed.
     */
    public function markAsCompleted(float $purchaseAmount): void
    {
        $this->status = 'completed';
        $this->completed_at = now();
        $this->referee_first_purchase = $purchaseAmount;
        $this->save();
    }

    /**
     * Mark as rewarded.
     */
    public function markAsRewarded(): void
    {
        $this->status = 'rewarded';
        $this->rewarded_at = now();
        $this->referrer_rewarded = true;
        $this->referee_rewarded = true;
        $this->save();
    }

    /**
     * Mark referrer as rewarded.
     */
    public function markReferrerRewarded(): void
    {
        $this->referrer_rewarded = true;
        $this->save();
    }

    /**
     * Mark referee as rewarded.
     */
    public function markRefereeRewarded(): void
    {
        $this->referee_rewarded = true;
        $this->save();
    }

    /**
     * Check if referral is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if referral is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if both parties have been rewarded.
     */
    public function isFullyRewarded(): bool
    {
        return $this->referrer_rewarded && $this->referee_rewarded;
    }

    /**
     * Check if purchase meets minimum requirement.
     */
    public function meetsPurchaseRequirement(): bool
    {
        $minPurchase = $this->referralProgram->min_referee_purchase;
        
        if (!$minPurchase) {
            return true;
        }

        return $this->referee_first_purchase >= $minPurchase;
    }
}
