<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductPriceHistory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'old_price',
        'new_price',
        'old_cost',
        'new_cost',
        'price_change',
        'price_change_percentage',
        'changed_by',
        'reason',
        'changed_at',
    ];

    protected function casts(): array
    {
        return [
            'old_price' => 'decimal:2',
            'new_price' => 'decimal:2',
            'old_cost' => 'decimal:2',
            'new_cost' => 'decimal:2',
            'price_change' => 'decimal:2',
            'price_change_percentage' => 'decimal:2',
            'changed_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the product that owns this price history.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who changed the price.
     */
    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    /**
     * Check if this was a price increase.
     */
    public function isPriceIncrease(): bool
    {
        return $this->price_change > 0;
    }

    /**
     * Check if this was a price decrease.
     */
    public function isPriceDecrease(): bool
    {
        return $this->price_change < 0;
    }

    /**
     * Get the absolute change amount.
     */
    public function getAbsoluteChange(): float
    {
        return abs($this->price_change);
    }

    /**
     * Get the absolute change percentage.
     */
    public function getAbsoluteChangePercentage(): float
    {
        return abs($this->price_change_percentage);
    }

    /**
     * Calculate old profit margin.
     */
    public function getOldProfitMargin(): ?float
    {
        if (!$this->old_cost || $this->old_cost == 0) {
            return null;
        }

        return (($this->old_price - $this->old_cost) / $this->old_cost) * 100;
    }

    /**
     * Calculate new profit margin.
     */
    public function getNewProfitMargin(): ?float
    {
        if (!$this->new_cost || $this->new_cost == 0) {
            return null;
        }

        return (($this->new_price - $this->new_cost) / $this->new_cost) * 100;
    }

    /**
     * Create a price history record from price changes.
     */
    public static function recordPriceChange(
        string $productId,
        float $oldPrice,
        float $newPrice,
        ?float $oldCost = null,
        ?float $newCost = null,
        ?string $changedBy = null,
        ?string $reason = null
    ): self {
        $priceChange = $newPrice - $oldPrice;
        $priceChangePercentage = $oldPrice > 0 ? (($priceChange / $oldPrice) * 100) : 0;

        return static::create([
            'product_id' => $productId,
            'old_price' => $oldPrice,
            'new_price' => $newPrice,
            'old_cost' => $oldCost,
            'new_cost' => $newCost,
            'price_change' => $priceChange,
            'price_change_percentage' => $priceChangePercentage,
            'changed_by' => $changedBy ?? auth()->id(),
            'reason' => $reason,
            'changed_at' => now(),
        ]);
    }

    /**
     * Scope to price increases.
     */
    public function scopeIncreases($query)
    {
        return $query->where('price_change', '>', 0);
    }

    /**
     * Scope to price decreases.
     */
    public function scopeDecreases($query)
    {
        return $query->where('price_change', '<', 0);
    }

    /**
     * Scope to recent changes.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('changed_at', '>=', now()->subDays($days));
    }

    /**
     * Scope to ordered by change date.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('changed_at', 'desc');
    }
}
