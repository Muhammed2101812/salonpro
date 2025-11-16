<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Coupon extends Model
{
    use HasFactory;
    use HasUuid;
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'code',
        'name',
        'description',
        'discount_type',
        'discount_value',
        'minimum_purchase',
        'maximum_discount',
        'start_date',
        'end_date',
        'usage_limit',
        'per_customer_limit',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_purchase' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'usage_limit' => 'integer',
        'per_customer_limit' => 'integer',
        'is_active' => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
