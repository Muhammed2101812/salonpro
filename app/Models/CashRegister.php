<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegister extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'branch_id',
        'register_name',
        'register_code',
        'opening_balance',
        'current_balance',
        'status',
        'current_session_id',
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

    public function sessions(): HasMany
    {
        return $this->hasMany(CashRegisterSession::class);
    }

    public function currentSession(): BelongsTo
    {
        return $this->belongsTo(CashRegisterSession::class, 'current_session_id');
    }

    // Helper Methods
    public function openSession(User $user, float $openingBalance, ?string $notes = null): CashRegisterSession
    {
        if ($this->status === 'open') {
            throw new \Exception('Kasa zaten açık');
        }

        $session = $this->sessions()->create([
            'opened_by' => $user->id,
            'opened_at' => now(),
            'opening_balance' => $openingBalance,
            'opening_notes' => $notes,
            'status' => 'open',
        ]);

        $this->update([
            'status' => 'open',
            'current_session_id' => $session->id,
            'current_balance' => $openingBalance,
        ]);

        return $session;
    }

    public function closeCurrentSession(User $user, float $closingBalance, ?string $notes = null): CashRegisterSession
    {
        if ($this->status !== 'open') {
            throw new \Exception('Kasa açık değil');
        }

        $session = $this->currentSession;
        $session->close($user, $closingBalance, $notes);

        $this->update([
            'status' => 'closed',
            'current_session_id' => null,
            'current_balance' => $closingBalance,
        ]);

        return $session;
    }

    public function addTransaction(array $data): CashRegisterTransaction
    {
        if ($this->status !== 'open') {
            throw new \Exception('Kasa açık değil');
        }

        $transaction = $this->currentSession->addTransaction($data);

        // Update register balance based on transaction type
        if (in_array($data['transaction_type'], ['sale', 'cash_in'])) {
            $this->current_balance += $data['amount'];
        } else {
            $this->current_balance -= $data['amount'];
        }
        $this->save();

        return $transaction;
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }
}
