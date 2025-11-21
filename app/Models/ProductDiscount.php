<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDiscount extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'product_id',
        'branch_id',
        'discount_name',
        'discount_type',
        'discount_value',
        'min_quantity',
        'max_quantity',
        'max_discount_amount',
        'start_date',
        'end_date',
        'start_time',
        'end_time',
        'applicable_days',
        'conditions',
        'usage_limit',
        'usage_count',
        'is_active',
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'min_quantity' => 'integer',
        'max_quantity' => 'integer',
        'max_discount_amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'applicable_days' => 'array',
        'conditions' => 'array',
        'usage_limit' => 'integer',
        'usage_count' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
