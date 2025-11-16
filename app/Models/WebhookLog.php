<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebhookLog extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'webhook_id',
        'event',
        'payload',
        'http_status',
        'response_body',
        'attempt',
        'duration_ms',
        'status',
        'error_message',
        'sent_at',
        'next_retry_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'http_status' => 'integer',
        'attempt' => 'integer',
        'duration_ms' => 'integer',
        'sent_at' => 'datetime',
        'next_retry_at' => 'datetime',
    ];

    public function webhook(): BelongsTo
    {
        return $this->belongsTo(Webhook::class);
    }
}
