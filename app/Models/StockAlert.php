<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAlert extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'product_id',
        'branch_id',
        'alert_type',
        'current_quantity',
        'threshold_quantity',
        'expiry_date',
        'severity',
        'status',
        'acknowledged_by',
        'acknowledged_at',
        'resolution_notes',
    ];

    protected function casts(): array
    {
        return [
            'current_quantity' => 'integer',
            'threshold_quantity' => 'integer',
            'expiry_date' => 'date',
            'acknowledged_at' => 'datetime',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the product for the alert.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the branch for the alert.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who acknowledged the alert.
     */
    public function acknowledgedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'acknowledged_by');
    }

    /**
     * Check if the alert is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the alert is critical.
     */
    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    /**
     * Check if the alert is high severity.
     */
    public function isHighSeverity(): bool
    {
        return in_array($this->severity, ['high', 'critical']);
    }

    /**
     * Acknowledge the alert.
     */
    public function acknowledge(string $userId, ?string $notes = null): bool
    {
        $this->status = 'acknowledged';
        $this->acknowledged_by = $userId;
        $this->acknowledged_at = now();

        if ($notes !== null) {
            $this->resolution_notes = $notes;
        }

        return $this->save();
    }

    /**
     * Resolve the alert.
     */
    public function resolve(?string $notes = null): bool
    {
        $this->status = 'resolved';

        if ($notes !== null) {
            $this->resolution_notes = $notes;
        }

        return $this->save();
    }

    /**
     * Ignore the alert.
     */
    public function ignore(?string $notes = null): bool
    {
        $this->status = 'ignored';

        if ($notes !== null) {
            $this->resolution_notes = $notes;
        }

        return $this->save();
    }

    /**
     * Get days until expiry.
     */
    public function getDaysUntilExpiry(): ?int
    {
        if ($this->expiry_date === null) {
            return null;
        }

        return now()->diffInDays($this->expiry_date, false);
    }

    /**
     * Check if the product is expired.
     */
    public function isExpired(): bool
    {
        return $this->expiry_date !== null && $this->expiry_date->isPast();
    }

    /**
     * Scope a query to only include active alerts.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope a query to only include critical alerts.
     */
    public function scopeCritical($query)
    {
        return $query->where('severity', 'critical');
    }

    /**
     * Scope a query to only include high severity alerts.
     */
    public function scopeHighSeverity($query)
    {
        return $query->whereIn('severity', ['high', 'critical']);
    }

    /**
     * Scope a query to only include alerts for a specific branch.
     */
    public function scopeForBranch($query, string $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope a query to only include alerts of a specific type.
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('alert_type', $type);
    }
}
