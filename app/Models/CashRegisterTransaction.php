<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class CashRegisterTransaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'session_id',
        'transaction_type',
        'amount',
        'reference_id',
        'reference_type',
        'payment_method',
        'description',
        'user_id',
        'transaction_time',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_time' => 'datetime',
    ];

    // Relationships
    public function session(): BelongsTo
    {
        return $this->belongsTo(CashRegisterSession::class, 'session_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // Helper Methods
    public function isCashIn(): bool
    {
        return in_array($this->transaction_type, ['sale', 'cash_in', 'opening']);
    }

    public function isCashOut(): bool
    {
        return in_array($this->transaction_type, ['refund', 'cash_out', 'closing']);
    }

    public function getSignedAmount(): float
    {
        return $this->isCashIn() ? $this->amount : -$this->amount;
    }

    // Scopes
    public function scopeForSession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeByPaymentMethod($query, string $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeCashIn($query)
    {
        return $query->whereIn('transaction_type', ['sale', 'cash_in', 'opening']);
    }

    public function scopeCashOut($query)
    {
        return $query->whereIn('transaction_type', ['refund', 'cash_out', 'closing']);
    }

    public function scopeSales($query)
    {
        return $query->where('transaction_type', 'sale');
    }

    public function scopeRefunds($query)
    {
        return $query->where('transaction_type', 'refund');
    }
}
