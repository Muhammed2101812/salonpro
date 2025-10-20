<?php

namespace App\Models;

use App\Models\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class NotificationQueue extends Model
{
    use HasFactory, SoftDeletes, LogsActivity, BranchScoped;

    protected $fillable = [
        'branch_id',
        'template_id',
        'campaign_id',
        'recipient_type',
        'recipient_id',
        'channel',
        'recipient_contact',
        'subject',
        'content',
        'status',
        'scheduled_at',
        'sent_at',
        'failed_at',
        'error_message',
        'provider_id',
        'provider_message_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'channel', 'sent_at'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(NotificationTemplate::class);
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(NotificationCampaign::class);
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }

    public function log(): BelongsTo
    {
        return $this->belongsTo(NotificationLog::class, 'id', 'queue_id');
    }
}
