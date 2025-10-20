<?php

namespace App\Models;

use App\Models\Traits\BranchScoped;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class NotificationPreference extends Model
{
    use HasFactory, LogsActivity, BranchScoped;

    protected $fillable = [
        'branch_id',
        'user_type',
        'user_id',
        'notification_type',
        'channel',
        'is_enabled',
        'frequency',
        'quiet_hours_start',
        'quiet_hours_end',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
        'is_enabled' => 'boolean',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['notification_type', 'channel', 'is_enabled'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function user(): MorphTo
    {
        return $this->morphTo();
    }
}
