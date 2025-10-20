<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
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
        'coupon_code',
        'coupon_name',
        'description',
        'discount_type',
        'discount_value',
        'min_purchase_amount',
        'max_discount_amount',
        'valid_from',
        'valid_until',
        'usage_limit',
        'usage_limit_per_customer',
        'total_used',
        'applicable_services',
        'applicable_products',
        'applicable_days',
        'applicable_time_start',
        'applicable_time_end',
        'first_time_customer_only',
        'is_active',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_purchase_amount' => 'decimal:2',
            'max_discount_amount' => 'decimal:2',
            'valid_from' => 'date',
            'valid_until' => 'date',
            'usage_limit' => 'integer',
            'usage_limit_per_customer' => 'integer',
            'total_used' => 'integer',
            'applicable_services' => 'array',
            'applicable_products' => 'array',
            'applicable_days' => 'array',
            'first_time_customer_only' => 'boolean',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the branch that owns the coupon.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created the coupon.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the coupon usages.
     */
    public function usages(): HasMany
    {
        return $this->hasMany(CouponUsage::class);
    }

    /**
     * Check if coupon is valid.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        $today = now()->toDateString();
        
        if ($today < $this->valid_from->toDateString()) {
            return false;
        }

        if ($this->valid_until && $today > $this->valid_until->toDateString()) {
            return false;
        }

        if ($this->usage_limit && $this->total_used >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Check if customer can use this coupon.
     */
    public function canBeUsedByCustomer(string $customerId): bool
    {
        if (!$this->isValid()) {
            return false;
        }

        $customerUsageCount = $this->usages()
            ->where('customer_id', $customerId)
            ->count();

        return $customerUsageCount < $this->usage_limit_per_customer;
    }

    /**
     * Calculate discount amount.
     */
    public function calculateDiscount(float $orderAmount): float
    {
        if ($this->min_purchase_amount && $orderAmount < $this->min_purchase_amount) {
            return 0;
        }

        $discount = 0;

        switch ($this->discount_type) {
            case 'percentage':
                $discount = $orderAmount * ((float)$this->discount_value / 100);
                break;
            case 'fixed':
                $discount = (float)$this->discount_value;
                break;
            case 'free_service':
            case 'free_product':
                // Handle in business logic
                break;
        }

        if ($this->max_discount_amount && $discount > $this->max_discount_amount) {
            $discount = (float)$this->max_discount_amount;
        }

        return min($discount, $orderAmount);
    }

    /**
     * Check if coupon is applicable on current day.
     */
    public function isApplicableToday(): bool
    {
        if (!$this->applicable_days) {
            return true;
        }

        $today = now()->dayOfWeek; // 0-6 (Sunday-Saturday)
        return in_array($today, $this->applicable_days);
    }

    /**
     * Check if coupon is applicable at current time.
     */
    public function isApplicableNow(): bool
    {
        if (!$this->applicable_time_start || !$this->applicable_time_end) {
            return true;
        }

        $now = now()->format('H:i:s');
        return $now >= $this->applicable_time_start && $now <= $this->applicable_time_end;
    }

    /**
     * Increment usage count.
     */
    public function incrementUsage(): void
    {
        $this->increment('total_used');
    }

    /**
     * Get remaining usage count.
     */
    public function getRemainingUsage(): ?int
    {
        if (!$this->usage_limit) {
            return null;
        }

        return max(0, $this->usage_limit - $this->total_used);
    }
}
