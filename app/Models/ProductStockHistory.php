<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductStockHistory extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'branch_id',
        'movement_type',
        'quantity_before',
        'quantity_change',
        'quantity_after',
        'reference_id',
        'reference_type',
        'user_id',
        'notes',
        'movement_date',
    ];

    protected function casts(): array
    {
        return [
            'quantity_before' => 'integer',
            'quantity_change' => 'integer',
            'quantity_after' => 'integer',
            'movement_date' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the product that owns this stock history.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the branch.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who made the movement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the reference model (polymorphic).
     */
    public function reference()
    {
        if (!$this->reference_type || !$this->reference_id) {
            return null;
        }

        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }

    /**
     * Check if this was a stock increase.
     */
    public function isIncrease(): bool
    {
        return $this->quantity_change > 0;
    }

    /**
     * Check if this was a stock decrease.
     */
    public function isDecrease(): bool
    {
        return $this->quantity_change < 0;
    }

    /**
     * Get the absolute quantity change.
     */
    public function getAbsoluteChange(): int
    {
        return abs($this->quantity_change);
    }

    /**
     * Record a stock movement.
     */
    public static function recordMovement(
        string $productId,
        string $branchId,
        string $movementType,
        int $quantityBefore,
        int $quantityChange,
        ?string $referenceId = null,
        ?string $referenceType = null,
        ?string $userId = null,
        ?string $notes = null
    ): self {
        return static::create([
            'product_id' => $productId,
            'branch_id' => $branchId,
            'movement_type' => $movementType,
            'quantity_before' => $quantityBefore,
            'quantity_change' => $quantityChange,
            'quantity_after' => $quantityBefore + $quantityChange,
            'reference_id' => $referenceId,
            'reference_type' => $referenceType,
            'user_id' => $userId ?? auth()->id(),
            'notes' => $notes,
            'movement_date' => now(),
        ]);
    }

    /**
     * Scope by movement type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('movement_type', $type);
    }

    /**
     * Scope to stock increases.
     */
    public function scopeIncreases($query)
    {
        return $query->where('quantity_change', '>', 0);
    }

    /**
     * Scope to stock decreases.
     */
    public function scopeDecreases($query)
    {
        return $query->where('quantity_change', '<', 0);
    }

    /**
     * Scope to recent movements.
     */
    public function scopeRecent($query, int $days = 30)
    {
        return $query->where('movement_date', '>=', now()->subDays($days));
    }

    /**
     * Scope to ordered by movement date.
     */
    public function scopeLatest($query)
    {
        return $query->orderBy('movement_date', 'desc');
    }

    /**
     * Get available movement types.
     */
    public static function getAvailableTypes(): array
    {
        return [
            'purchase' => 'Satın Alma',
            'sale' => 'Satış',
            'adjustment' => 'Düzeltme',
            'transfer_in' => 'Transfer (Gelen)',
            'transfer_out' => 'Transfer (Giden)',
            'return' => 'İade',
            'loss' => 'Kayıp/Fire',
            'production' => 'Üretim',
            'consumption' => 'Kullanım',
        ];
    }
}
