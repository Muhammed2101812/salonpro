<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'code',
        'rate',
        'description',
        'is_default',
        'is_active',
        'effective_from',
        'effective_until',
    ];

    protected $casts = [
        'rate' => 'decimal:2',
        'is_default' => 'boolean',
        'is_active' => 'boolean',
        'effective_from' => 'date',
        'effective_until' => 'date',
    ];

    // Helper Methods
    public function isEffective(?string $date = null): bool
    {
        $checkDate = $date ? \Carbon\Carbon::parse($date) : now();

        if ($this->effective_from && $checkDate->isBefore($this->effective_from)) {
            return false;
        }

        if ($this->effective_until && $checkDate->isAfter($this->effective_until)) {
            return false;
        }

        return true;
    }

    public function calculateTaxAmount(float $amount): float
    {
        return (float) ($amount * ($this->rate / 100));
    }

    public function getAmountWithTax(float $amount): float
    {
        return $amount + $this->calculateTaxAmount($amount);
    }

    public function getAmountWithoutTax(float $amountWithTax): float
    {
        return (float) ($amountWithTax / (1 + ($this->rate / 100)));
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeEffectiveOn($query, ?string $date = null)
    {
        $checkDate = $date ?? now()->toDateString();

        return $query->where(function ($q) use ($checkDate) {
            $q->whereNull('effective_from')
                ->orWhere('effective_from', '<=', $checkDate);
        })->where(function ($q) use ($checkDate) {
            $q->whereNull('effective_until')
                ->orWhere('effective_until', '>=', $checkDate);
        });
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        // When setting a tax rate as default, unset others
        static::saving(function ($taxRate) {
            if ($taxRate->is_default) {
                static::where('id', '!=', $taxRate->id)
                    ->update(['is_default' => false]);
            }
        });
    }
}
