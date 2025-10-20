<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class BankTransaction extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'bank_account_id',
        'transaction_number',
        'transaction_date',
        'transaction_type',
        'amount',
        'balance_before',
        'balance_after',
        'reference_id',
        'reference_type',
        'payee_payer',
        'description',
        'notes',
        'status',
        'created_by',
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'balance_before' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    // Relationships
    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }

    // Helper Methods
    public function cancel(): bool
    {
        if ($this->status === 'cancelled') {
            return false;
        }

        // Reverse the transaction on the bank account
        $bankAccount = $this->bankAccount;
        
        if (in_array($this->transaction_type, ['deposit', 'interest'])) {
            $bankAccount->current_balance -= $this->amount;
        } else {
            $bankAccount->current_balance += $this->amount;
        }

        $bankAccount->save();
        
        $this->status = 'cancelled';
        return $this->save();
    }

    public function reconcile(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $this->status = 'reconciled';
        return $this->save();
    }

    public function isDeposit(): bool
    {
        return in_array($this->transaction_type, ['deposit', 'interest', 'transfer']);
    }

    public function isWithdrawal(): bool
    {
        return in_array($this->transaction_type, ['withdrawal', 'fee']);
    }

    // Scopes
    public function scopeForAccount($query, $accountId)
    {
        return $query->where('bank_account_id', $accountId);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('transaction_type', $type);
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeDeposits($query)
    {
        return $query->whereIn('transaction_type', ['deposit', 'interest']);
    }

    public function scopeWithdrawals($query)
    {
        return $query->whereIn('transaction_type', ['withdrawal', 'fee']);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (!$transaction->transaction_number) {
                $transaction->transaction_number = 'BT-' . now()->format('Ymd') . '-' . str_pad(
                    static::whereDate('created_at', now())->count() + 1,
                    4,
                    '0',
                    STR_PAD_LEFT
                );
            }
        });
    }
}
