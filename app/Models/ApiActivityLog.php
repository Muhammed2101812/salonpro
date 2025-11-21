<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiActivityLog extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'user_id',
        'token_id',
        'ip_address',
        'user_agent',
        'method',
        'endpoint',
        'request_data',
        'response_data',
        'http_status',
        'response_time_ms',
        'requested_at',
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
        'http_status' => 'integer',
        'response_time_ms' => 'integer',
        'requested_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
