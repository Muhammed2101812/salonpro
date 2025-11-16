<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PerformanceMetric extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'branch_id',
        'metric_name',
        'metric_category',
        'metric_value',
        'unit',
        'metadata',
        'measured_at',
    ];

    protected $casts = [
        'metric_value' => 'decimal:4',
        'metadata' => 'array',
        'measured_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
