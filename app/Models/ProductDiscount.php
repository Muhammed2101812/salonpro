<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDiscount extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'branch_id',
        'discount_name',
        'discount_type',
        'discount_value',
        'min_quantity',
        'max_quantity',
        'max_discount_amount',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'applicable_days',
        'conditions',
        'usage_limit',
        'usage_count',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'discount_value' => 'decimal:2',
            'min_quantity' => 'integer',
            'max_quantity' => 'integer',
            'max_discount_amount' => 'decimal:2',
            'start_date' => 'date',
            'end_date' => 'date',
            'applicable_days' => 'array',
            'conditions' => 'array',
            'usage_limit' => 'integer',
            'usage_count' => 'integer',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the product that owns the discount.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the branch that owns the discount.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Check if discount is valid now.
     */
    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }

        if (!$this->isWithinDateRange()) {
            return false;
        }

        if (!$this->isWithinTimeRange()) {
            return false;
        }

        if (!$this->isWithinApplicableDays()) {
            return false;
        }

        if ($this->hasReachedUsageLimit()) {
            return false;
        }

        return true;
    }

    /**
     * Check if current date is within discount date range.
     */
    public function isWithinDateRange(): bool
    {
        $now = Carbon::today();

        if ($this->start_date && $now->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $now->gt($this->end_date)) {
            return false;
        }

        return true;
    }

    /**
     * Check if current time is within discount time range.
     */
    public function isWithinTimeRange(): bool
    {
        if (!$this->start_time || !$this->end_time) {
            return true;
        }

        $now = Carbon::now()->format('H:i:s');

        return $now >= $this->start_time && $now <= $this->end_time;
    }

    /**
     * Check if today is an applicable day.
     */
    public function isWithinApplicableDays(): bool
    {
        if (!$this->applicable_days || empty($this->applicable_days)) {
            return true;
        }

        $today = strtolower(Carbon::now()->format('l'));

        return in_array($today, array_map('strtolower', $this->applicable_days));
    }

    /**
     * Check if usage limit has been reached.
     */
    public function hasReachedUsageLimit(): bool
    {
        if (!$this->usage_limit) {
            return false;
        }

        return $this->usage_count >= $this->usage_limit;
    }

    /**
     * Calculate discount amount for given price and quantity.
     */
    public function calculateDiscountAmount(float $price, int $quantity = 1): float
    {
        if (!$this->isValid()) {
            return 0;
        }

        if ($quantity < $this->min_quantity) {
            return 0;
        }

        if ($this->max_quantity && $quantity > $this->max_quantity) {
            return 0;
        }

        $discountAmount = 0;

        switch ($this->discount_type) {
            case 'percentage':
                $discountAmount = ($price * $quantity) * ($this->discount_value / 100);
                if ($this->max_discount_amount) {
                    $discountAmount = min($discountAmount, $this->max_discount_amount);
                }
                break;

            case 'fixed':
                $discountAmount = $this->discount_value * $quantity;
                break;

            case 'buy_x_get_y':
                // Implement buy X get Y logic if needed
                break;

            case 'bundle':
                // Implement bundle logic if needed
                break;
        }

        return round($discountAmount, 2);
    }

    /**
     * Increment usage count.
     */
    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    /**
     * Check if discount is global (not branch-specific).
     */
    public function isGlobal(): bool
    {
        return $this->branch_id === null;
    }

    /**
     * Scope to active discounts.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to valid discounts.
     */
    public function scopeValid($query)
    {
        $today = Carbon::today();

        return $query->where('is_active', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('start_date')
                    ->orWhere('start_date', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', $today);
            })
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('usage_count', '<', 'usage_limit');
            });
    }

    /**
     * Scope to global discounts.
     */
    public function scopeGlobal($query)
    {
        return $query->whereNull('branch_id');
    }

    /**
     * Get available discount types.
     */
    public static function getAvailableTypes(): array
    {
        return [
            'percentage' => 'Yüzde',
            'fixed' => 'Sabit Tutar',
            'buy_x_get_y' => 'X Al Y Kazan',
            'bundle' => 'Paket',
        ];
    }
}
