<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServicePackage extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'description',
        'total_price',
        'discount_percentage',
        'final_price',
        'validity_days',
        'max_uses',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'total_price' => 'decimal:2',
            'discount_percentage' => 'decimal:2',
            'final_price' => 'decimal:2',
            'validity_days' => 'integer',
            'max_uses' => 'integer',
            'is_active' => 'boolean',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class, 'package_service', 'package_id', 'service_id')
            ->withPivot('quantity', 'price_override')
            ->withTimestamps();
    }

    /**
     * Calculate final price based on total price and discount percentage
     */
    public function calculateFinalPrice(): float
    {
        $discount = $this->total_price * ($this->discount_percentage / 100);
        return round($this->total_price - $discount, 2);
    }

    /**
     * Check if package is still valid
     */
    public function isValid(): bool
    {
        return $this->is_active;
    }
}
