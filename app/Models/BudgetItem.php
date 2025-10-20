<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BudgetItem extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'budget_plan_id',
        'account_id',
        'category_name',
        'budgeted_amount',
        'actual_amount',
        'variance',
        'variance_percentage',
        'notes',
    ];

    protected $casts = [
        'budgeted_amount' => 'decimal:2',
        'actual_amount' => 'decimal:2',
        'variance' => 'decimal:2',
        'variance_percentage' => 'decimal:2',
    ];

    // Relationships
    public function budgetPlan(): BelongsTo
    {
        return $this->belongsTo(BudgetPlan::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(ChartOfAccount::class, 'account_id');
    }

    // Helper Methods
    public function recalculate(): void
    {
        // Get actual amount from journal entries for this account within budget period
        $budgetPlan = $this->budgetPlan;
        
        $actualAmount = JournalEntryLine::where('account_id', $this->account_id)
            ->whereHas('journalEntry', function ($query) use ($budgetPlan) {
                $query->where('status', 'posted')
                    ->whereBetween('entry_date', [
                        $budgetPlan->start_date,
                        $budgetPlan->end_date
                    ]);
            })
            ->sum('amount');

        $this->actual_amount = $actualAmount;
        $this->variance = $this->actual_amount - $this->budgeted_amount;

        if ($this->budgeted_amount > 0) {
            $this->variance_percentage = ($this->variance / $this->budgeted_amount) * 100;
        } else {
            $this->variance_percentage = 0;
        }

        $this->save();
    }

    public function getUtilizationPercentage(): float
    {
        if ($this->budgeted_amount <= 0) {
            return 0;
        }

        return (float) (($this->actual_amount / $this->budgeted_amount) * 100);
    }

    public function getRemainingBudget(): float
    {
        return (float) ($this->budgeted_amount - $this->actual_amount);
    }

    public function isOverBudget(): bool
    {
        return $this->actual_amount > $this->budgeted_amount;
    }

    public function isUnderBudget(): bool
    {
        return $this->actual_amount < $this->budgeted_amount;
    }

    public function isOnTrack(): bool
    {
        $budgetPlan = $this->budgetPlan;
        $totalDays = $budgetPlan->start_date->diffInDays($budgetPlan->end_date);
        $daysElapsed = $budgetPlan->start_date->diffInDays(now());

        if ($totalDays <= 0) {
            return true;
        }

        $expectedPercentage = ($daysElapsed / $totalDays) * 100;
        $actualPercentage = $this->getUtilizationPercentage();

        // Allow 10% tolerance
        return abs($actualPercentage - $expectedPercentage) <= 10;
    }

    // Scopes
    public function scopeForBudget($query, $budgetId)
    {
        return $query->where('budget_plan_id', $budgetId);
    }

    public function scopeForAccount($query, $accountId)
    {
        return $query->where('account_id', $accountId);
    }

    public function scopeOverBudget($query)
    {
        return $query->whereRaw('actual_amount > budgeted_amount');
    }

    public function scopeUnderBudget($query)
    {
        return $query->whereRaw('actual_amount < budgeted_amount');
    }

    // Boot method
    protected static function boot()
    {
        parent::boot();

        static::saved(function ($item) {
            $item->budgetPlan->recalculateAll();
        });

        static::deleted(function ($item) {
            $item->budgetPlan->recalculateAll();
        });
    }
}
