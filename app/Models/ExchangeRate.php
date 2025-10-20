<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExchangeRate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'from_currency',
        'to_currency',
        'rate',
        'date',
        'source',
    ];

    protected $casts = [
        'rate' => 'decimal:6',
        'date' => 'date',
    ];

    /**
     * Get the from currency.
     */
    public function fromCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'from_currency', 'code');
    }

    /**
     * Get the to currency.
     */
    public function toCurrency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'to_currency', 'code');
    }

    /**
     * Get rate for a specific currency pair and date.
     */
    public static function getRate(string $from, string $to, ?string $date = null): ?float
    {
        $query = self::where('from_currency', $from)
            ->where('to_currency', $to);

        if ($date) {
            $query->where('date', $date);
        }

        return $query->latest('date')->value('rate');
    }

    /**
     * Get latest rate for a currency pair.
     */
    public static function getLatestRate(string $from, string $to): ?float
    {
        return self::where('from_currency', $from)
            ->where('to_currency', $to)
            ->latest('date')
            ->value('rate');
    }

    /**
     * Scope to filter by date range.
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('date', [$startDate, $endDate]);
    }

    /**
     * Scope to filter by currency pair.
     */
    public function scopeCurrencyPair($query, string $from, string $to)
    {
        return $query->where('from_currency', $from)
            ->where('to_currency', $to);
    }
}
