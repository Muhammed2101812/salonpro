<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Currency extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'code',
        'name',
        'symbol',
        'symbol_position',
        'decimal_places',
        'thousands_separator',
        'decimal_separator',
        'exchange_rate',
        'is_base',
        'is_active',
    ];

    protected $casts = [
        'decimal_places' => 'integer',
        'exchange_rate' => 'decimal:6',
        'is_base' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get exchange rates from this currency.
     */
    public function exchangeRatesFrom(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'from_currency', 'code');
    }

    /**
     * Get exchange rates to this currency.
     */
    public function exchangeRatesTo(): HasMany
    {
        return $this->hasMany(ExchangeRate::class, 'to_currency', 'code');
    }

    /**
     * Get active currencies only.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the base currency.
     */
    public function scopeBase($query)
    {
        return $query->where('is_base', true)->first();
    }

    /**
     * Set this currency as base.
     */
    public function setAsBase(): void
    {
        // Remove base from all currencies
        self::query()->update(['is_base' => false]);
        
        // Set this as base with rate 1
        $this->update([
            'is_base' => true,
            'exchange_rate' => 1,
        ]);
    }

    /**
     * Format amount with currency symbol.
     */
    public function format(float $amount): string
    {
        $formatted = number_format(
            $amount,
            $this->decimal_places,
            $this->decimal_separator,
            $this->thousands_separator
        );

        return $this->symbol_position === 'before'
            ? $this->symbol . $formatted
            : $formatted . $this->symbol;
    }

    /**
     * Convert amount from this currency to another.
     */
    public function convertTo(float $amount, Currency $toCurrency): float
    {
        // Convert to base currency first
        $baseAmount = $amount / $this->exchange_rate;
        
        // Then convert to target currency
        return $baseAmount * $toCurrency->exchange_rate;
    }
}
