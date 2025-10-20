<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAuditItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'stock_audit_id',
        'product_id',
        'system_quantity',
        'actual_quantity',
        'difference',
        'variance_type',
        'unit_cost',
        'value_adjustment',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'system_quantity' => 'integer',
            'actual_quantity' => 'integer',
            'difference' => 'integer',
            'unit_cost' => 'decimal:2',
            'value_adjustment' => 'decimal:2',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the stock audit that owns the item.
     */
    public function stockAudit(): BelongsTo
    {
        return $this->belongsTo(StockAudit::class);
    }

    /**
     * Get the product for this item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Check if quantities match.
     */
    public function isMatch(): bool
    {
        return $this->variance_type === 'match';
    }

    /**
     * Check if there's a shortage.
     */
    public function isShortage(): bool
    {
        return $this->variance_type === 'shortage';
    }

    /**
     * Check if there's an overage.
     */
    public function isOverage(): bool
    {
        return $this->variance_type === 'overage';
    }

    /**
     * Get absolute difference.
     */
    public function getAbsoluteDifference(): int
    {
        return abs($this->difference);
    }

    /**
     * Get variance percentage.
     */
    public function getVariancePercentage(): float
    {
        if ($this->system_quantity === 0) {
            return $this->actual_quantity > 0 ? 100 : 0;
        }

        return round((abs($this->difference) / $this->system_quantity) * 100, 2);
    }

    /**
     * Calculate and set variance.
     */
    public function calculateVariance(): void
    {
        $this->difference = $this->actual_quantity - $this->system_quantity;

        if ($this->difference === 0) {
            $this->variance_type = 'match';
        } elseif ($this->difference < 0) {
            $this->variance_type = 'shortage';
        } else {
            $this->variance_type = 'overage';
        }

        $this->value_adjustment = $this->difference * $this->unit_cost;
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->calculateVariance();
        });
    }
}
