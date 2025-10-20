<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegisterSession extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'cash_register_id',
        'opened_by',
        'closed_by',
        'opened_at',
        'closed_at',
        'opening_balance',
        'closing_balance',
        'expected_closing_balance',
        'difference',
        'total_cash_in',
        'total_cash_out',
        'transaction_count',
        'opening_notes',
        'closing_notes',
        'status',
    ];

    protected $casts = [
        'opened_at' => 'datetime',
        'closed_at' => 'datetime',
        'opening_balance' => 'decimal:2',
        'closing_balance' => 'decimal:2',
        'expected_closing_balance' => 'decimal:2',
        'difference' => 'decimal:2',
        'total_cash_in' => 'decimal:2',
        'total_cash_out' => 'decimal:2',
        'transaction_count' => 'integer',
    ];

    // Relationships
    public function cashRegister(): BelongsTo
    {
        return $this->belongsTo(CashRegister::class);
    }

    public function openedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(CashRegisterTransaction::class, 'session_id');
    }

    // Helper Methods
    public function close(User $user, float $closingBalance, ?string $notes = null): void
    {
        if ($this->status !== 'open') {
            throw new \Exception('Oturum zaten kapalı');
        }

        $expectedBalance = $this->calculateExpectedBalance();

        $this->update([
            'closed_by' => $user->id,
            'closed_at' => now(),
            'closing_balance' => $closingBalance,
            'expected_closing_balance' => $expectedBalance,
            'difference' => $closingBalance - $expectedBalance,
            'closing_notes' => $notes,
            'status' => 'closed',
        ]);
    }

    public function reconcile(): void
    {
        if ($this->status !== 'closed') {
            throw new \Exception('Oturum kapalı değil');
        }

        $this->status = 'reconciled';
        $this->save();
    }

    public function addTransaction(array $data): CashRegisterTransaction
    {
        if ($this->status !== 'open') {
            throw new \Exception('Oturum açık değil');
        }

        $transaction = $this->transactions()->create(array_merge($data, [
            'transaction_time' => $data['transaction_time'] ?? now(),
            'user_id' => $data['user_id'] ?? auth()->id(),
        ]));

        // Update session totals
        if (in_array($data['transaction_type'], ['sale', 'cash_in'])) {
            $this->total_cash_in += $data['amount'];
        } else {
            $this->total_cash_out += $data['amount'];
        }
        
        $this->transaction_count++;
        $this->save();

        return $transaction;
    }

    public function calculateExpectedBalance(): float
    {
        return (float) ($this->opening_balance + $this->total_cash_in - $this->total_cash_out);
    }

    public function getDuration(): ?\DateInterval
    {
        if (!$this->closed_at) {
            return $this->opened_at->diff(now());
        }

        return $this->opened_at->diff($this->closed_at);
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function isReconciled(): bool
    {
        return $this->status === 'reconciled';
    }

    public function hasVariance(): bool
    {
        return $this->difference && abs($this->difference) > 0.01;
    }

    // Scopes
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeReconciled($query)
    {
        return $query->where('status', 'reconciled');
    }

    public function scopeForRegister($query, $registerId)
    {
        return $query->where('cash_register_id', $registerId);
    }

    public function scopeInDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('opened_at', [$startDate, $endDate]);
    }
}
