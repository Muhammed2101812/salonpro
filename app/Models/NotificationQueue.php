<?php

declare(strict_types=1);

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationQueue extends Model
{
    use HasFactory;
    use HasUuid;

    protected $table = 'notification_queue';

    protected $fillable = [
        'template_id',
        'recipient_type',
        'recipient_id',
        'channel',
        'recipient_address',
        'subject',
        'message',
        'scheduled_at',
        'sent_at',
        'status',
        'attempts',
        'error_message',
        'metadata',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'attempts' => 'integer',
        'metadata' => 'array',
    ];

    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class);
    }
}
