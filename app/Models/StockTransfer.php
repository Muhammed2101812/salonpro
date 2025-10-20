<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransfer extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'transfer_number',
        'from_branch_id',
        'to_branch_id',
        'product_id',
        'quantity',
        'status',
        'transfer_date',
        'expected_arrival_date',
        'actual_arrival_date',
        'reason',
        'notes',
        'requested_by',
        'approved_by',
        'approved_at',
        'received_by',
        'received_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'transfer_date' => 'date',
            'expected_arrival_date' => 'date',
            'actual_arrival_date' => 'date',
            'approved_at' => 'datetime',
            'received_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the source branch.
     */
    public function fromBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'from_branch_id');
    }

    /**
     * Get the destination branch.
     */
    public function toBranch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'to_branch_id');
    }

    /**
     * Get the product being transferred.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the user who requested the transfer.
     */
    public function requester(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    /**
     * Get the user who approved the transfer.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the user who received the transfer.
     */
    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    /**
     * Check if the transfer is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the transfer is in transit.
     */
    public function isInTransit(): bool
    {
        return $this->status === 'in_transit';
    }

    /**
     * Check if the transfer is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'received';
    }

    /**
     * Check if the transfer is rejected.
     */
    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    /**
     * Check if the transfer can be approved.
     */
    public function canBeApproved(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if the transfer can be received.
     */
    public function canBeReceived(): bool
    {
        return $this->status === 'in_transit';
    }

    /**
     * Check if the transfer is overdue.
     */
    public function isOverdue(): bool
    {
        if ($this->expected_arrival_date === null) {
            return false;
        }

        return $this->expected_arrival_date->isPast()
            && !in_array($this->status, ['received', 'rejected', 'cancelled']);
    }

    /**
     * Approve the transfer.
     */
    public function approve(string $userId): bool
    {
        if (!$this->canBeApproved()) {
            return false;
        }

        $this->status = 'in_transit';
        $this->approved_by = $userId;
        $this->approved_at = now();

        return $this->save();
    }

    /**
     * Receive the transfer.
     */
    public function receive(string $userId): bool
    {
        if (!$this->canBeReceived()) {
            return false;
        }

        $this->status = 'received';
        $this->received_by = $userId;
        $this->received_at = now();
        $this->actual_arrival_date = now()->toDateString();

        return $this->save();
    }

    /**
     * Reject the transfer.
     */
    public function reject(string $reason): bool
    {
        if (!in_array($this->status, ['pending', 'in_transit'])) {
            return false;
        }

        $this->status = 'rejected';
        $this->rejection_reason = $reason;

        return $this->save();
    }

    /**
     * Cancel the transfer.
     */
    public function cancel(): bool
    {
        if (!in_array($this->status, ['pending', 'in_transit'])) {
            return false;
        }

        $this->status = 'cancelled';

        return $this->save();
    }

    /**
     * Get transit duration in days.
     */
    public function getTransitDuration(): ?int
    {
        if ($this->approved_at === null || $this->received_at === null) {
            return null;
        }

        return $this->approved_at->diffInDays($this->received_at);
    }

    /**
     * Scope a query to only include transfers from a specific branch.
     */
    public function scopeFromBranch($query, string $branchId)
    {
        return $query->where('from_branch_id', $branchId);
    }

    /**
     * Scope a query to only include transfers to a specific branch.
     */
    public function scopeToBranch($query, string $branchId)
    {
        return $query->where('to_branch_id', $branchId);
    }

    /**
     * Scope a query to only include transfers for a specific branch (from or to).
     */
    public function scopeForBranch($query, string $branchId)
    {
        return $query->where(function ($q) use ($branchId) {
            $q->where('from_branch_id', $branchId)
                ->orWhere('to_branch_id', $branchId);
        });
    }

    /**
     * Scope a query to only include pending transfers.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include in-transit transfers.
     */
    public function scopeInTransit($query)
    {
        return $query->where('status', 'in_transit');
    }

    /**
     * Scope a query to only include overdue transfers.
     */
    public function scopeOverdue($query)
    {
        return $query->where('expected_arrival_date', '<', now())
            ->whereNotIn('status', ['received', 'rejected', 'cancelled']);
    }
}
