<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePricingRule extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'service_id',
        'rule_name',
        'rule_type',
        'conditions',
        'adjustment_type',
        'adjustment_value',
        'priority',
        'valid_from',
        'valid_until',
        'is_active',
    ];

    protected $casts = [
        'conditions' => 'array',
        'adjustment_value' => 'decimal:2',
        'priority' => 'integer',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'is_active' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}
