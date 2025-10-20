<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BudgetPlan extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'branch_id',
        'budget_name',
        'budget_period',
        'fiscal_year',
        'start_date',
        'end_date',
        'total_budget',
        'total_actual',
        'variance',
        'variance_percentage',
        'status',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'fiscal_year' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'total_budget' => 'decimal:2',
        'total_actual' => 'decimal:2',
        'variance' => 'decimal:2',
        'variance_percentage' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(BudgetItem::class);
    }

    // Helper Methods
    public function approve(User $user): bool
    {
        if ($this->status === 'approved') {
            throw new \Exception('Bütçe zaten onaylanmış');
        }

        $this->update([
            'status' => 'approved',
            'approved_by' => $user->id,
            'approved_at' => now(),
        ]);

        return true;
    }

    public function activate(): bool
    {
        if ($this->status !== 'approved') {
            throw new \Exception('Sadece onaylanmış bütçeler aktif hale getirilebilir');
        }

        $this->status = 'active';
        return $this->save();
    }

    public function close(): bool
    {
        if ($this->status !== 'active') {
            throw new \Exception('Sadece aktif bütçeler kapatılabilir');
        }

        $this->recalculateAll();
        $this->status = 'closed';
        return $this->save();
    }

    public function recalculateAll(): void
    {
        // Recalculate all items
        foreach ($this->items as $item) {
            $item->recalculate();
        }

        // Recalculate totals
        $this->total_budget = $this->items()->sum('budgeted_amount');
        $this->total_actual = $this->items()->sum('actual_amount');
        $this->variance = $this->total_actual - $this->total_budget;
        
        if ($this->total_budget > 0) {
            $this->variance_percentage = ($this->variance / $this->total_budget) * 100;
        } else {
            $this->variance_percentage = 0;
        }

        $this->save();
    }

    public function getUtilizationPercentage(): float
    {
        if ($this->total_budget <= 0) {
            return 0;
        }

        return (float) (($this->total_actual / $this->total_budget) * 100);
    }

    public function isOverBudget(): bool
    {
        return $this->total_actual > $this->total_budget;
    }

    public function isUnderBudget(): bool
    {
        return $this->total_actual < $this->total_budget;
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeClosed($query)
    {
        return $query->where('status', 'closed');
    }

    public function scopeForBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeForFiscalYear($query, int $year)
    {
        return $query->where('fiscal_year', $year);
    }

    public function scopeByPeriod($query, string $period)
    {
        return $query->where('budget_period', $period);
    }

    public function scopeOverBudget($query)
    {
        return $query->whereRaw('total_actual > total_budget');
    }
}
