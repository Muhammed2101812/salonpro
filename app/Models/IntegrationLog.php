<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IntegrationLog extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'integration_id',
        'action',
        'direction',
        'request_data',
        'response_data',
        'http_status',
        'duration_ms',
        'status',
        'error_message',
        'executed_at',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'http_status' => 'integer',
        'duration_ms' => 'integer',
        'executed_at' => 'datetime',
    ];

    public function integration(): BelongsTo
    {
        return $this->belongsTo(Integration::class);
    }
}
