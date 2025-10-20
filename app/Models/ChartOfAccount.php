<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChartOfAccount extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'account_code',
        'account_name',
        'account_type',
        'account_subtype',
        'parent_account_id',
        'level',
        'opening_balance',
        'current_balance',
        'description',
        'is_system_account',
        'is_active',
    ];

    protected $casts = [
        'level' => 'integer',
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_system_account' => 'boolean',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function parentAccount(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'parent_account_id');
    }

    public function subAccounts(): HasMany
    {
        return $this->hasMany(ChartOfAccount::class, 'parent_account_id');
    }

    public function journalEntryLines(): HasMany
    {
        return $this->hasMany(JournalEntryLine::class, 'account_id');
    }

    public function budgetItems(): HasMany
    {
        return $this->hasMany(BudgetItem::class, 'account_id');
    }

    // Helper Methods
    public function isAsset(): bool
    {
        return $this->account_type === 'asset';
    }

    public function isLiability(): bool
    {
        return $this->account_type === 'liability';
    }

    public function isEquity(): bool
    {
        return $this->account_type === 'equity';
    }

    public function isRevenue(): bool
    {
        return $this->account_type === 'revenue';
    }

    public function isExpense(): bool
    {
        return $this->account_type === 'expense';
    }

    public function debit(float $amount): void
    {
        // Assets and Expenses increase with debits
        if (in_array($this->account_type, ['asset', 'expense', 'cost_of_sales'])) {
            $this->current_balance += $amount;
        } else {
            // Liabilities, Equity, and Revenue decrease with debits
            $this->current_balance -= $amount;
        }
        $this->save();
    }

    public function credit(float $amount): void
    {
        // Liabilities, Equity, and Revenue increase with credits
        if (in_array($this->account_type, ['liability', 'equity', 'revenue'])) {
            $this->current_balance += $amount;
        } else {
            // Assets and Expenses decrease with credits
            $this->current_balance -= $amount;
        }
        $this->save();
    }

    public function getFullAccountCode(): string
    {
        $codes = [$this->account_code];
        $parent = $this->parentAccount;

        while ($parent) {
            array_unshift($codes, $parent->account_code);
            $parent = $parent->parentAccount;
        }

        return implode('.', $codes);
    }

    public function getFullAccountName(): string
    {
        $names = [$this->account_name];
        $parent = $this->parentAccount;

        while ($parent) {
            array_unshift($names, $parent->account_name);
            $parent = $parent->parentAccount;
        }

        return implode(' > ', $names);
    }

    public function hasSubAccounts(): bool
    {
        return $this->subAccounts()->count() > 0;
    }

    public function canBeDeleted(): bool
    {
        return !$this->is_system_account && 
               !$this->hasSubAccounts() && 
               $this->journalEntryLines()->count() === 0;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('account_type', $type);
    }

    public function scopeBySubtype($query, string $subtype)
    {
        return $query->where('account_subtype', $subtype);
    }

    public function scopeMainAccounts($query)
    {
        return $query->where('level', 0);
    }

    public function scopeSubAccounts($query)
    {
        return $query->where('level', '>', 0);
    }

    public function scopeSystemAccounts($query)
    {
        return $query->where('is_system_account', true);
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($account) {
            if (!$account->canBeDeleted()) {
                throw new \Exception('Bu hesap silinemez: Sistem hesabı, alt hesapları var veya yevmiye kayıtları mevcut');
            }
        });
    }
}
