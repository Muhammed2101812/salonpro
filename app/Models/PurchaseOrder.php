<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'supplier_id',
        'po_number',
        'order_date',
        'expected_delivery_date',
        'actual_delivery_date',
        'status',
        'subtotal',
        'tax_amount',
        'shipping_cost',
        'discount_amount',
        'total_amount',
        'notes',
        'terms_and_conditions',
        'created_by',
        'approved_by',
        'approved_at',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'expected_delivery_date' => 'date',
            'actual_delivery_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax_amount' => 'decimal:2',
            'shipping_cost' => 'decimal:2',
            'discount_amount' => 'decimal:2',
            'total_amount' => 'decimal:2',
            'approved_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * Get the branch that owns the purchase order.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the supplier for the purchase order.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user who created the purchase order.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who approved the purchase order.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Get the items for the purchase order.
     */
    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Check if the purchase order is fully received.
     */
    public function isFullyReceived(): bool
    {
        return $this->status === 'received';
    }

    /**
     * Check if the purchase order is partially received.
     */
    public function isPartiallyReceived(): bool
    {
        return $this->status === 'partial';
    }

    /**
     * Check if the purchase order can be edited.
     */
    public function isEditable(): bool
    {
        return in_array($this->status, ['draft', 'sent']);
    }

    /**
     * Check if the purchase order is approved.
     */
    public function isApproved(): bool
    {
        return $this->approved_by !== null && $this->approved_at !== null;
    }

    /**
     * Check if the purchase order is overdue.
     */
    public function isOverdue(): bool
    {
        if ($this->expected_delivery_date === null) {
            return false;
        }

        return $this->expected_delivery_date->isPast()
            && !in_array($this->status, ['received', 'cancelled']);
    }

    /**
     * Get total items count.
     */
    public function getTotalItemsCount(): int
    {
        return $this->items()->sum('quantity_ordered');
    }

    /**
     * Get total received items count.
     */
    public function getTotalReceivedCount(): int
    {
        return $this->items()->sum('quantity_received');
    }

    /**
     * Get completion percentage.
     */
    public function getCompletionPercentage(): float
    {
        $totalOrdered = $this->getTotalItemsCount();

        if ($totalOrdered === 0) {
            return 0;
        }

        $totalReceived = $this->getTotalReceivedCount();

        return round(($totalReceived / $totalOrdered) * 100, 2);
    }

    /**
     * Scope a query to only include orders for a specific branch.
     */
    public function scopeForBranch($query, string $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope a query to only include orders with a specific status.
     */
    public function scopeWithStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include overdue orders.
     */
    public function scopeOverdue($query)
    {
        return $query->where('expected_delivery_date', '<', now())
            ->whereNotIn('status', ['received', 'cancelled']);
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'sent', 'confirmed', 'partial']);
    }
}
