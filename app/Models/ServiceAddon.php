<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ServiceAddon extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'description',
        'price',
        'duration',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'duration' => 'integer',
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
        return $this->belongsToMany(Service::class, 'service_addon', 'addon_id', 'service_id')
            ->withPivot('price_override')
            ->withTimestamps();
    }

    /**
     * Get the effective price for a specific service (considering price override)
     */
    public function getEffectivePrice(?float $priceOverride = null): float
    {
        return $priceOverride ?? $this->price;
    }

    /**
     * Check if addon is available
     */
    public function isAvailable(): bool
    {
        return $this->is_active && !$this->trashed();
    }

    /**
     * Get formatted duration
     */
    public function getFormattedDuration(): string
    {
        if ($this->duration === 0) {
            return 'Süre eklenmez';
        }

        return $this->duration . ' dakika';
    }
}
