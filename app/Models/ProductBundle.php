<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductBundle extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'sku',
        'description',
        'bundle_price',
        'original_total_price',
        'discount_amount',
        'discount_percentage',
        'quantity_available',
        'valid_from',
        'valid_until',
        'is_active',
        'image_url',
    ];

    protected function casts(): array
    {
        return [
            'bundle_price' => 'decimal:2',
            'original_total_price' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'quantity_available' => 'integer',
            'valid_from' => 'date',
            'valid_until' => 'date',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the branch that owns the bundle.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the products in this bundle.
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_bundle_items', 'bundle_id', 'product_id')
            ->withPivot('quantity', 'individual_price')
            ->withTimestamps();
    }

    /**
     * Check if the bundle is currently valid.
     */
    public function isValid(): bool
    {
        $now = now()->toDateString();

        if ($this->valid_from !== null && $now < $this->valid_from->toDateString()) {
            return false;
        }

        if ($this->valid_until !== null && $now > $this->valid_until->toDateString()) {
            return false;
        }

        return true;
    }

    /**
     * Check if the bundle is available for purchase.
     */
    public function isAvailable(): bool
    {
        return $this->is_active && $this->isValid() && $this->quantity_available > 0;
    }

    /**
     * Check if the bundle is expired.
     */
    public function isExpired(): bool
    {
        if ($this->valid_until === null) {
            return false;
        }

        return $this->valid_until->isPast();
    }

    /**
     * Get the savings amount.
     */
    public function getSavings(): float
    {
        return (float) $this->discount_amount;
    }

    /**
     * Get the savings percentage.
     */
    public function getSavingsPercentage(): float
    {
        return (float) $this->discount_percentage;
    }

    /**
     * Calculate quantity available based on component products.
     */
    public function calculateAvailableQuantity(): int
    {
        $minQuantity = PHP_INT_MAX;

        foreach ($this->products as $product) {
            $requiredQuantity = $product->pivot->quantity;
            $availableQuantity = $product->stock_quantity;

            if ($requiredQuantity > 0) {
                $possibleBundles = floor($availableQuantity / $requiredQuantity);
                $minQuantity = min($minQuantity, $possibleBundles);
            }
        }

        return $minQuantity === PHP_INT_MAX ? 0 : (int) $minQuantity;
    }

    /**
     * Update the quantity available.
     */
    public function updateAvailableQuantity(): bool
    {
        $this->quantity_available = $this->calculateAvailableQuantity();

        return $this->save();
    }

    /**
     * Calculate discount from prices.
     */
    public function calculateDiscount(): void
    {
        if ($this->original_total_price > 0) {
            $this->discount_amount = $this->original_total_price - $this->bundle_price;
            $this->discount_percentage = round(
                ($this->discount_amount / $this->original_total_price) * 100,
                2
            );
        }
    }

    /**
     * Check if bundle has sufficient stock.
     */
    public function hasSufficientStock(int $quantity = 1): bool
    {
        foreach ($this->products as $product) {
            $requiredQuantity = $product->pivot->quantity * $quantity;

            if ($product->stock_quantity < $requiredQuantity) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get total items count in bundle.
     */
    public function getTotalItemsCount(): int
    {
        return $this->products->sum('pivot.quantity');
    }

    /**
     * Scope a query to only include active bundles.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include valid bundles.
     */
    public function scopeValid($query)
    {
        $now = now()->toDateString();

        return $query->where(function ($q) use ($now) {
            $q->whereNull('valid_from')->orWhere('valid_from', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('valid_until')->orWhere('valid_until', '>=', $now);
        });
    }

    /**
     * Scope a query to only include available bundles.
     */
    public function scopeAvailable($query)
    {
        return $query->active()->valid()->where('quantity_available', '>', 0);
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($bundle) {
            $bundle->calculateDiscount();
        });
    }
}
