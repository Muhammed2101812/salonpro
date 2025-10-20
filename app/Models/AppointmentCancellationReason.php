<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AppointmentCancellationReason extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'reason',
        'description',
        'requires_note',
        'is_customer_fault',
        'is_active',
        'usage_count',
    ];

    protected $casts = [
        'requires_note' => 'boolean',
        'is_customer_fault' => 'boolean',
        'is_active' => 'boolean',
        'usage_count' => 'integer',
    ];

    public function cancellations(): HasMany
    {
        return $this->hasMany(AppointmentCancellation::class, 'reason_id');
    }

    public function isActive(): bool
    {
        return $this->is_active;
    }

    public function requiresNote(): bool
    {
        return $this->requires_note;
    }

    public function isCustomerFault(): bool
    {
        return $this->is_customer_fault;
    }

    public function incrementUsage(): void
    {
        $this->increment('usage_count');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeMostUsed($query, int $limit = 10)
    {
        return $query->orderBy('usage_count', 'desc')->limit($limit);
    }
}
