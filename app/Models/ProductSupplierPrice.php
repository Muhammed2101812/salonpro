<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSupplierPrice extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'supplier_id',
        'supplier_sku',
        'price',
        'currency',
        'minimum_order_quantity',
        'lead_time_days',
        'is_preferred',
        'price_valid_from',
        'price_valid_until',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'minimum_order_quantity' => 'integer',
            'lead_time_days' => 'integer',
            'is_preferred' => 'boolean',
            'price_valid_from' => 'date',
            'price_valid_until' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the product that owns this supplier price.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the supplier.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Check if price is currently valid.
     */
    public function isPriceValid(): bool
    {
        $today = Carbon::today();

        if ($this->price_valid_from && $today->lt($this->price_valid_from)) {
            return false;
        }

        if ($this->price_valid_until && $today->gt($this->price_valid_until)) {
            return false;
        }

        return true;
    }

    /**
     * Get estimated delivery date based on lead time.
     */
    public function getEstimatedDeliveryDate(): ?Carbon
    {
        if (!$this->lead_time_days) {
            return null;
        }

        return Carbon::today()->addDays($this->lead_time_days);
    }

    /**
     * Calculate total price for given quantity.
     */
    public function calculateTotalPrice(int $quantity): float
    {
        return $this->price * max($quantity, $this->minimum_order_quantity);
    }

    /**
     * Set this as the preferred supplier.
     */
    public function setAsPreferred(): void
    {
        // Remove preferred flag from other suppliers for this product
        static::where('product_id', $this->product_id)
            ->where('id', '!=', $this->id)
            ->update(['is_preferred' => false]);

        $this->update(['is_preferred' => true]);
    }

    /**
     * Scope to preferred suppliers.
     */
    public function scopePreferred($query)
    {
        return $query->where('is_preferred', true);
    }

    /**
     * Scope to valid prices.
     */
    public function scopeValidPrices($query)
    {
        $today = Carbon::today();

        return $query->where(function ($q) use ($today) {
            $q->whereNull('price_valid_from')
                ->orWhere('price_valid_from', '<=', $today);
        })
            ->where(function ($q) use ($today) {
                $q->whereNull('price_valid_until')
                    ->orWhere('price_valid_until', '>=', $today);
            });
    }

    /**
     * Scope by currency.
     */
    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }

    /**
     * Get available currencies.
     */
    public static function getAvailableCurrencies(): array
    {
        return [
            'TRY' => 'Türk Lirası',
            'USD' => 'ABD Doları',
            'EUR' => 'Euro',
        ];
    }
}
