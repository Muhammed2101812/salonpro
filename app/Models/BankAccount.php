<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class BankAccount extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'account_name',
        'bank_name',
        'account_number',
        'iban',
        'swift_code',
        'currency',
        'opening_balance',
        'current_balance',
        'account_type',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(BankTransaction::class);
    }

    // Helper Methods
    public function deposit(float $amount, array $transactionData = []): BankTransaction
    {
        $balanceBefore = $this->current_balance;
        $this->current_balance += $amount;
        $this->save();

        return $this->transactions()->create(array_merge($transactionData, [
            'transaction_type' => 'deposit',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->current_balance,
            'transaction_date' => $transactionData['transaction_date'] ?? now(),
            'created_by' => $transactionData['created_by'] ?? auth()->id(),
        ]));
    }

    public function withdraw(float $amount, array $transactionData = []): BankTransaction
    {
        if ($this->current_balance < $amount) {
            throw new \Exception('Yetersiz bakiye');
        }

        $balanceBefore = $this->current_balance;
        $this->current_balance -= $amount;
        $this->save();

        return $this->transactions()->create(array_merge($transactionData, [
            'transaction_type' => 'withdrawal',
            'amount' => $amount,
            'balance_before' => $balanceBefore,
            'balance_after' => $this->current_balance,
            'transaction_date' => $transactionData['transaction_date'] ?? now(),
            'created_by' => $transactionData['created_by'] ?? auth()->id(),
        ]));
    }

    public function transfer(BankAccount $toAccount, float $amount, array $transactionData = []): array
    {
        if ($this->current_balance < $amount) {
            throw new \Exception('Yetersiz bakiye');
        }

        // Withdraw from this account
        $withdrawTransaction = $this->withdraw($amount, array_merge($transactionData, [
            'transaction_type' => 'transfer',
            'description' => 'Transfer to ' . $toAccount->account_name,
        ]));

        // Deposit to target account
        $depositTransaction = $toAccount->deposit($amount, array_merge($transactionData, [
            'transaction_type' => 'transfer',
            'description' => 'Transfer from ' . $this->account_name,
        ]));

        return [
            'from' => $withdrawTransaction,
            'to' => $depositTransaction,
        ];
    }

    public function calculateBalance(): float
    {
        $deposits = $this->transactions()
            ->whereIn('transaction_type', ['deposit', 'interest'])
            ->where('status', 'completed')
            ->sum('amount');

        $withdrawals = $this->transactions()
            ->whereIn('transaction_type', ['withdrawal', 'fee'])
            ->where('status', 'completed')
            ->sum('amount');

        return (float) ($this->opening_balance + $deposits - $withdrawals);
    }

    public function reconcile(): bool
    {
        $calculatedBalance = $this->calculateBalance();
        $this->current_balance = $calculatedBalance;
        return $this->save();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeCompanyAccounts($query)
    {
        return $query->whereNull('branch_id');
    }

    public function scopeByCurrency($query, string $currency)
    {
        return $query->where('currency', $currency);
    }
}
