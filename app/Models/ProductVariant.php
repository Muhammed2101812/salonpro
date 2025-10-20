<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductVariant extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'product_id',
        'variant_name',
        'sku',
        'barcode',
        'attributes',
        'price',
        'cost',
        'stock_quantity',
        'min_stock_level',
        'max_stock_level',
        'reorder_point',
        'weight_grams',
        'image_url',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'attributes' => 'array',
            'price' => 'decimal:2',
            'cost' => 'decimal:2',
            'stock_quantity' => 'integer',
            'min_stock_level' => 'integer',
            'max_stock_level' => 'integer',
            'reorder_point' => 'integer',
            'weight_grams' => 'integer',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the parent product.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if variant is low on stock.
     */
    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->min_stock_level;
    }

    /**
     * Check if variant is out of stock.
     */
    public function isOutOfStock(): bool
    {
        return $this->stock_quantity <= 0;
    }

    /**
     * Check if variant needs reordering.
     */
    public function needsReorder(): bool
    {
        return $this->reorder_point && $this->stock_quantity <= $this->reorder_point;
    }

    /**
     * Check if variant is at max stock level.
     */
    public function isAtMaxStock(): bool
    {
        return $this->max_stock_level && $this->stock_quantity >= $this->max_stock_level;
    }

    /**
     * Get the profit margin.
     */
    public function getProfitMargin(): ?float
    {
        if (!$this->cost || $this->cost == 0) {
            return null;
        }

        return (($this->price - $this->cost) / $this->cost) * 100;
    }

    /**
     * Get the profit amount.
     */
    public function getProfit(): ?float
    {
        if (!$this->cost) {
            return null;
        }

        return $this->price - $this->cost;
    }

    /**
     * Get attribute value by key.
     */
    public function getAttribute(string $key): mixed
    {
        return $this->attributes[$key] ?? null;
    }

    /**
     * Scope to active variants.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to variants needing reorder.
     */
    public function scopeNeedsReorder($query)
    {
        return $query->whereNotNull('reorder_point')
            ->whereColumn('stock_quantity', '<=', 'reorder_point');
    }

    /**
     * Scope to low stock variants.
     */
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'min_stock_level');
    }
}
