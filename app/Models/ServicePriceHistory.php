<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePriceHistory extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'service_id',
        'old_price',
        'new_price',
        'price_change',
        'price_change_percentage',
        'changed_by',
        'reason',
        'changed_at',
    ];

    protected $casts = [
        'old_price' => 'decimal:2',
        'new_price' => 'decimal:2',
        'price_change' => 'decimal:2',
        'price_change_percentage' => 'decimal:2',
        'changed_at' => 'datetime',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
