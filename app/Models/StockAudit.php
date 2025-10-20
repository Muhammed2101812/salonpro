<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockAudit extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'branch_id',
        'audit_number',
        'audit_date',
        'status',
        'audit_type',
        'total_products_counted',
        'discrepancies_found',
        'total_value_adjustment',
        'notes',
        'conducted_by',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'audit_date' => 'date',
            'total_products_counted' => 'integer',
            'discrepancies_found' => 'integer',
            'total_value_adjustment' => 'decimal:2',
            'approved_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the branch for the audit.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who conducted the audit.
     */
    public function conductor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conducted_by');
    }

    /**
     * Get the user who approved the audit.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the items for the audit.
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockAuditItem::class);
    }

    /**
     * Check if the audit is scheduled.
     */
    public function isScheduled(): bool
    {
        return $this->status === 'scheduled';
    }

    /**
     * Check if the audit is in progress.
     */
    public function isInProgress(): bool
    {
        return $this->status === 'in_progress';
    }

    /**
     * Check if the audit is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if the audit is approved.
     */
    public function isApproved(): bool
    {
        return $this->approved_by !== null && $this->approved_at !== null;
    }

    /**
     * Check if the audit can be edited.
     */
    public function isEditable(): bool
    {
        return in_array($this->status, ['scheduled', 'in_progress']);
    }

    /**
     * Start the audit.
     */
    public function start(): bool
    {
        if ($this->status !== 'scheduled') {
            return false;
        }

        $this->status = 'in_progress';

        return $this->save();
    }

    /**
     * Complete the audit.
     */
    public function complete(): bool
    {
        if ($this->status !== 'in_progress') {
            return false;
        }

        $this->status = 'completed';
        $this->calculateTotals();

        return $this->save();
    }

    /**
     * Approve the audit.
     */
    public function approve(string $userId): bool
    {
        if ($this->status !== 'completed') {
            return false;
        }

        $this->approved_by = $userId;
        $this->approved_at = now();

        return $this->save();
    }

    /**
     * Cancel the audit.
     */
    public function cancel(): bool
    {
        if (!in_array($this->status, ['scheduled', 'in_progress'])) {
            return false;
        }

        $this->status = 'cancelled';

        return $this->save();
    }

    /**
     * Calculate totals from items.
     */
    public function calculateTotals(): void
    {
        $items = $this->items;

        $this->total_products_counted = $items->count();
        $this->discrepancies_found = $items->where('variance_type', '!=', 'match')->count();
        $this->total_value_adjustment = $items->sum('value_adjustment');
    }

    /**
     * Get discrepancy rate percentage.
     */
    public function getDiscrepancyRate(): float
    {
        if ($this->total_products_counted === 0) {
            return 0;
        }

        return round(($this->discrepancies_found / $this->total_products_counted) * 100, 2);
    }

    /**
     * Get items with shortages.
     */
    public function getShortages()
    {
        return $this->items()->where('variance_type', 'shortage')->get();
    }

    /**
     * Get items with overages.
     */
    public function getOverages()
    {
        return $this->items()->where('variance_type', 'overage')->get();
    }

    /**
     * Scope a query to only include audits for a specific branch.
     */
    public function scopeForBranch($query, string $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope a query to only include completed audits.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include audits with discrepancies.
     */
    public function scopeWithDiscrepancies($query)
    {
        return $query->where('discrepancies_found', '>', 0);
    }
}
