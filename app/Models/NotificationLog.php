<?php

namespace App\Models;

use App\Models\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NotificationLog extends Model
{
    use HasFactory, BranchScoped;

    protected $fillable = [
        'branch_id',
        'queue_id',
        'recipient_type',
        'recipient_id',
        'channel',
        'status',
        'sent_at',
        'delivered_at',
        'read_at',
        'failed_at',
        'error_message',
        'provider_response',
        'cost',
        'metadata',
    ];

    protected $casts = [
        'provider_response' => 'array',
        'metadata' => 'array',
        'sent_at' => 'datetime',
        'delivered_at' => 'datetime',
        'read_at' => 'datetime',
        'failed_at' => 'datetime',
        'cost' => 'decimal:4',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
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
