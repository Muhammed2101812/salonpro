<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoyaltyPointTransaction extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'loyalty_points_id',
        'transaction_type',
        'points',
        'balance_after',
        'reference_id',
        'reference_type',
        'description',
        'expiry_date',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'points' => 'integer',
            'balance_after' => 'integer',
            'expiry_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the loyalty points record.
     */
    public function loyaltyPoints(): BelongsTo
    {
        return $this->belongsTo(LoyaltyPoint::class, 'loyalty_points_id');
    }

    /**
     * Get the user who created the transaction.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if transaction is a credit (positive).
     */
    public function isCredit(): bool
    {
        return $this->points > 0;
    }

    /**
     * Check if transaction is a debit (negative).
     */
    public function isDebit(): bool
    {
        return $this->points < 0;
    }

    /**
     * Get absolute points value.
     */
    public function getAbsolutePoints(): int
    {
        return abs($this->points);
    }

    /**
     * Check if points are expiring soon (within 30 days).
     */
    public function isExpiringSoon(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return now()->diffInDays($this->expiry_date, false) <= 30 && now()->diffInDays($this->expiry_date, false) > 0;
    }

    /**
     * Check if points have expired.
     */
    public function hasExpired(): bool
    {
        if (!$this->expiry_date) {
            return false;
        }

        return now() > $this->expiry_date;
    }
}
