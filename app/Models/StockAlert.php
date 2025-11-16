<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockAlert extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'branch_id',
        'product_id',
        'alert_type',
        'threshold_quantity',
        'current_quantity',
        'priority',
        'status',
        'notified_at',
        'resolved_at',
        'notes',
    ];

    protected $casts = [
        'threshold_quantity' => 'decimal:2',
        'current_quantity' => 'decimal:2',
        'priority' => 'integer',
        'notified_at' => 'datetime',
        'resolved_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
