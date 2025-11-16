<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationLog extends Model
{
    use HasFactory;
    use HasUuid;

    protected $fillable = [
        'template_id',
        'queue_id',
        'recipient_type',
        'recipient_id',
        'channel',
        'recipient_address',
        'subject',
        'message',
        'sent_at',
        'delivered_at',
        'read_at',
        'failed_at',
        'status',
        'error_message',
        'provider_response',
        'metadata',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'failed_at' => 'datetime',
        'provider_response' => 'array',
        'metadata' => 'array',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class);
    }

    public function queue(): BelongsTo
    {
        return $this->belongsTo(NotificationQueue::class);
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }
}
