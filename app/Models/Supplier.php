<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'website',
        'address',
        'city',
        'country',
        'tax_number',
        'tax_office',
        'contact_person',
        'payment_terms',
        'credit_limit',
        'current_balance',
        'rating',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'contact_person' => 'array',
            'credit_limit' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'rating' => 'integer',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the purchase orders for the supplier.
     */
    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Check if supplier has available credit.
     */
    public function hasAvailableCredit(): bool
    {
        if ($this->credit_limit === null) {
            return true;
        }

        return $this->current_balance < $this->credit_limit;
    }

    /**
     * Get remaining credit limit.
     */
    public function getRemainingCredit(): ?float
    {
        if ($this->credit_limit === null) {
            return null;
        }

        return max(0, (float) $this->credit_limit - (float) $this->current_balance);
    }

    /**
     * Update current balance.
     */
    public function updateBalance(float $amount): void
    {
        $this->increment('current_balance', $amount);
    }

    /**
     * Check if supplier is highly rated (4-5 stars).
     */
    public function isHighlyRated(): bool
    {
        return $this->rating !== null && $this->rating >= 4;
    }

    /**
     * Scope a query to only include active suppliers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include highly rated suppliers.
     */
    public function scopeHighlyRated($query)
    {
        return $query->where('rating', '>=', 4);
    }
}
