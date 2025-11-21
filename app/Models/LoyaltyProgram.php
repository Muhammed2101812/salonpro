<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyProgram extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'name',
        'description',
        'points_per_currency',
        'currency_per_point',
        'minimum_points_to_redeem',
        'points_expiry_months',
        'tier_system_enabled',
        'tiers',
        'is_active',
    ];

    protected $casts = [
        'points_per_currency' => 'decimal:2',
        'currency_per_point' => 'decimal:2',
        'minimum_points_to_redeem' => 'integer',
        'points_expiry_months' => 'integer',
        'tier_system_enabled' => 'boolean',
        'tiers' => 'array',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
