<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class CustomerSegment extends Model
{
    use HasFactory;
    use HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'branch_id',
        'segment_name',
        'description',
        'filter_criteria',
        'segment_type',
        'customer_count',
        'last_calculated_at',
        'auto_update',
        'is_active',
        'created_by',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'filter_criteria' => 'array',
            'customer_count' => 'integer',
            'last_calculated_at' => 'datetime',
            'auto_update' => 'boolean',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Get the branch that owns the segment.
     */
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Get the user who created the segment.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the customers in this segment (for static segments).
     */
    public function customers(): BelongsToMany
    {
        return $this->belongsToMany(Customer::class, 'customer_segment_members', 'segment_id', 'customer_id')
            ->withPivot('added_at', 'added_by')
            ->withTimestamps();
    }

    /**
     * Check if segment is dynamic.
     */
    public function isDynamic(): bool
    {
        return $this->segment_type === 'dynamic';
    }

    /**
     * Check if segment is static.
     */
    public function isStatic(): bool
    {
        return $this->segment_type === 'static';
    }

    /**
     * Update customer count.
     */
    public function updateCustomerCount(): void
    {
        if ($this->isStatic()) {
            $this->customer_count = $this->customers()->count();
        }
        
        $this->last_calculated_at = now();
        $this->save();
    }

    /**
     * Add customer to static segment.
     */
    public function addCustomer(string $customerId, ?string $addedBy = null): void
    {
        if ($this->isStatic()) {
            $this->customers()->attach($customerId, [
                'added_at' => now(),
                'added_by' => $addedBy,
            ]);
            $this->updateCustomerCount();
        }
    }

    /**
     * Remove customer from static segment.
     */
    public function removeCustomer(string $customerId): void
    {
        if ($this->isStatic()) {
            $this->customers()->detach($customerId);
            $this->updateCustomerCount();
        }
    }
}
